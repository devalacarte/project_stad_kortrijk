var _currentPage = -1; //stores which page is currently being displayed; Zero based!
var _propositions; //stores all current propositions (array)
var AMOUNT_OF_ITEMS_PER_PAGE = 4;

//This function gets called when the ajax call returns
function handlePMUReply(response)
{
    var rows = "";
    var rewardsAmount = 0;
    try
    {
        //Parse the propositions
        parsedResponse = JSON.parse(response);
        //Get the values
        rows = parsedResponse["props"];
        rewardsAmount = parsedResponse["rewardsAmount"];
    }
    catch (ex)
    {
        //Not json: error message
        console.log("Error: Failed to parse current propositions: " + response);
    }

    showRewardsAmount(rewardsAmount);

    _propositions = rows;
    //If the array is empty, there aren't any current propositions
    if (rows.length == 0)
    {
        $("#divNoCurrentProps").show(); //Show the "no props" div
        $("#tblCurrentProps").hide(); //Hide the current props table
        $("#divPageNumbers").hide(); //Hide the page numbers
        
        return;
    }
    
    calculateAmountOfPagesAndDisplayNextPage(rows);
}

function calculateAmountOfPagesAndDisplayNextPage(props)
{
    var amountOfPages = Math.ceil(props.length / AMOUNT_OF_ITEMS_PER_PAGE);

    displayPageNumbers(amountOfPages);

    //If the new amount of pages is less than the current page, display page 0
    if (_currentPage + 1 > amountOfPages)
        _currentPage = 0;
    else
        _currentPage = (_currentPage + 1) % amountOfPages; //Cycle to next page

    
    displayPage(_currentPage);
}

//Called after getting a PHP update
function displayPageNumbers(amountOfPages)
{
    $("#divPageNumbers ul").empty(); //Clear the previous page numbers

    for (var i = 0; i < amountOfPages; i++) //Create the new page numbers
    {
        var pageNumberLi = document.createElement("li");
        var pageNumberText = document.createTextNode(i + 1);
        pageNumberLi.appendChild(pageNumberText);
        $("#divPageNumbers ul").append(pageNumberLi);
    }

    //Highlight the current page number
    $("#divPageNumbers ul li:nth-child(" + (_currentPage + 1) + ")").attr("class","currentPageNumber");
    $("#divPageNumbers").show(); //Show the page numbers
}

function displayPage(pageNumber)
{
    //Slice the propositions to be shown and display them
    var startIndex = pageNumber * AMOUNT_OF_ITEMS_PER_PAGE;
    var props = _propositions.slice(startIndex, startIndex + AMOUNT_OF_ITEMS_PER_PAGE);
    fillPropositionsTable(props);

    //Highlight the current page number
    $("#divPageNumbers ul li").attr("class", "pageNumber"); //Wipe any previous page number highlights first
    $("#divPageNumbers ul li:nth-child(" + (pageNumber + 1) + ")").attr("class","currentPageNumber");
}

function fillPropositionsTable(props)
{
    $("#divNoCurrentProps").hide();
    $("#tblCurrentProps-body").empty(); //Wipe the table
    
    for (var i = 0; i < props.length; i++)
    {
        var html = '<tr>' +
                            '<td>' + props[i]['id'] + '.</td>' +
                            '<td class="border-right small"><p>' + props[i]['description'] + '</p></td>' +
                            '<td class="align-center">' + props[i]['amountOfWinners'] + '</td>' +
                        '</tr>';
        //Add row to the table
        $(html).hide().appendTo("#tblCurrentProps-body").delay(i * 150).fadeIn(500);
    }

    $("#tblCurrentProps").show(); //Display the table when done
}

function showRewardsAmount(rewardsAmount)
{
    $("#amountOfRewards").html(rewardsAmount);
}

//Request the current propositions from the database
function requestUpdate()
{
    $.ajax({
        type: "post",
        url: "includes/pmuFunctions.php",
        data: { "function": "getPropsAndTotalRewards" },
        dataType: "text",
        success: function (response)
        {
            handlePMUReply(response);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            console.log("Error: Failed to request current propositions: " + errorThrown);
        }
    });
}

function calculateAmountOfItemsPerPage()
{
    var LINE_HEIGHT = 55
    var HTML_HEIGHT = 448;
    var MIN_AMOUNT_OF_ITEMS = 4;
    var windowHeight = $(window).height();

    //Calculate how many lines fit the screen
    var spaceForLines = windowHeight - HTML_HEIGHT;
    var amountOfItems =  Math.floor(spaceForLines / LINE_HEIGHT);

    //Enforce minimum amount of items
    if (amountOfItems < MIN_AMOUNT_OF_ITEMS)
        amountOfItems = MIN_AMOUNT_OF_ITEMS;

    //Resize the paper background
    $("#stats-body").height((amountOfItems + 1) * LINE_HEIGHT);

    return amountOfItems;
}

$(document).ready(function ()
{
    //Calculate how many items need to be shown per page based on the screen size
    AMOUNT_OF_ITEMS_PER_PAGE = calculateAmountOfItemsPerPage();
    //Get the propositions and total amount of rewards once the page has loaded
    requestUpdate();
});

$(window).resize(function ()
{
    //Calculate how many items need to be shown per page based on the screen size
    AMOUNT_OF_ITEMS_PER_PAGE = calculateAmountOfItemsPerPage();
    displayPage(_currentPage);
});