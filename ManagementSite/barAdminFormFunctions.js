function formhash(form, password) 
{
    return hex_sha512(password.value);
}

//Prepare the form elements
$(document).ready(function ()
{
    //Deactivate the admin pass submits by default
    $("#submit").attr("disabled", "disabled");
    $("#mainAdminNewPassSubmit").attr("disabled", "disabled");

    //Main admin pass change submit
    $("#mainAdminNewPassSubmit").click(function ()
    {
        //Hash the passwords
        var oldPassHash = formhash(this.form, this.form.mainAdminOldPass);
        var newPassHash = formhash(this.form, this.form.mainAdminNewPass);
        //Clear the input fields
        $("#mainAdminOldPass").val('');
        $("#mainAdminNewPass").val('');
        $("#mainAdminNewPassConfirm").val('');

        //Ajax post the values to changeAdminPass.php
        $.ajax(
        {
            type: "POST",
            url: "../includes/changeAdminPass.php",
            data: { oldPass: oldPassHash, newPass: newPassHash },
            success: function (response)
            {
                //Read the response
                var reply = "";
                try
                {
                    reply = JSON.parse(response);
                } catch (ex)
                {
                    console.log("Failed to parse json in barAdminFormFunctions.js - change admin pass: " + ex.message);
                }

                if (reply == "")
                    return;

                //Display the reply message
                $("#divMainAdminPassChangeMessage").html(reply["message"]);
                $("#divMainAdminPassChangeMessage").show();
                //Hide the message after some time
                setTimeout(function () { $("#divMainAdminPassChangeMessage").hide(); }, 5000);                
            }
        });

        return false; //disallow form submit redirect
    });

    //Bar admin pass change submit
    $('#submit').click(function ()
    {
        //Hash the new password
        var passHash = formhash(this.form, this.form.inputPassword);
        var inputUser = "barAdmin";

        //Clear the input fields
        $("#inputPassword").val('');
        $("#inputConfirm").val('');

        //Ajax post the values to register handler to update the password
        $.ajax(
        {
            type: "POST",
            url: "../includes/registerHandler.php",
            data: { p: passHash, inputUser: inputUser, isAdmin: 1, noRedirect: 1 },
            success: function ()
            {
                $("#divBarAdminPassChangeSuccess").show();
                //Hide the message after some time
                setTimeout(function () { $("#divBarAdminPassChangeSuccess").hide(); }, 5000);
            }
        });

        return false; //disallow form submit redirect
    });

    //Main admin new pass validation
    $('#mainAdminNewPass').keyup(function ()
    {
        var input = $(this).val();
        var confirmPassword = $("#mainAdminNewPassConfirm").val();
        var oldPassword = $("#mainAdminOldPass").val();
        if (oldPassword.trim() != "" && input.trim() != "" && confirmPassword == input)
            $("#mainAdminNewPassSubmit").removeAttr("disabled");
        else
            $("#mainAdminNewPassSubmit").attr("disabled", "disabled");
    });

    //Main admin new pass confirm validation
    $('#mainAdminNewPassConfirm').keyup(function ()
    {
        var input = $(this).val();
        var confirmPassword = $("#mainAdminNewPass").val();
        var oldPassword = $("#mainAdminOldPass").val();
        if (oldPassword.trim() != "" && input.trim() != "" && confirmPassword == input)
            $("#mainAdminNewPassSubmit").removeAttr("disabled");
        else
            $("#mainAdminNewPassSubmit").attr("disabled", "disabled");
    });

    //Bar admin new pass validation
    $('#inputPassword').keyup(function ()
    {
        var input = $(this).val();
        var regTest = new RegExp(/^\d{4}$/);
        var confirmPassword = $("#inputConfirm").val();
        if (regTest.test(input) == true && confirmPassword == input)
            $("#submit").removeAttr("disabled");
        else
            $("#submit").attr("disabled", "disabled");
    });

    //Bar admin new pass confirm validation
    $('#inputConfirm').keyup(function ()
    {
        var input = $(this).val();
        var regTest = new RegExp(/^\d{4}$/);
        var password = $("#inputPassword").val();
        if (regTest.test(input) == true && password == input)
            $("#submit").removeAttr("disabled");
        else
            $("#submit").attr("disabled", "disabled");
    });

    $("#btnToggleBarAdminPass").click(function ()
    {
        //Define image urls
        var chevronDownImage = "images/chevronDown.png";
        var chevronRightImage = "images/chevronRight.png";

        //Get the current state
        var image = $("img", this);
        var currentImage = image.attr("src");
        //Toggle image and visibility
        if (currentImage == chevronRightImage)
        {
            $("#divCollapsePaswordsContainer").show();
            image.attr("src", chevronDownImage);
        }
        else
        {
            $("#divCollapsePaswordsContainer").hide();
            image.attr("src", chevronRightImage);
        }

    });
});