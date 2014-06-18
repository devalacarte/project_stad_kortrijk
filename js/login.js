var _enforcePin = true;

function formhash(form, password) 
{
    var p = document.createElement("input");

    // nieuw element voor ons hashed paswoord op te slaan en door te sturen naar php
    form.appendChild(p);
    p.name = "p";
    p.type = "hidden";
    p.value = hex_sha512(password.value);

    // plaintext wachtwoord niet zenden
    password.value = "";
    //form.submit();
}

$(document).ready(function ()
{
    $("#submit").attr("disabled", "disabled");

    $('#inputPassword').keyup(function ()
    {
        var input = $(this).val();
        var regTest = new RegExp(/^\d{4}$/);
        if (_enforcePin)
        {
            if (regTest.test(input) == true)
                $("#submit").removeAttr("disabled");
            else
                $("#submit").attr("disabled", "disabled");
        }
        else
        {
            if(input != "")
                $("#submit").removeAttr("disabled");
            else
                $("#submit").attr("disabled", "disabled");
        }
            
    });

    $('#submit').click(function ()
    {
        formhash(this.form, this.form.inputPassword);
    });
});