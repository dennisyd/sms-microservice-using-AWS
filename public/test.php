<?php

?>

<body onload="getURLParam('name');">

<script language="javascript">

    function getURLParam(name) {
        name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");

        var regexS = "[\\?&]"+name+"=([^&#]*)";
        //console.log(regexS);
        var regex = new RegExp( regexS );
        //regex = regexS;
        var results = regex.exec( window.location.href );
        if( results == null )
            return "";
        else

            return  HTMLEncode(results[1]);
    }

    //var loginParam = decodeURI(getURLParam('login'));
    //var reminderSentParam = decodeURI(getURLParam('reminder-sent'));

    //if((typeof loginParam != "undefined" && loginParam == "failed") || (typeof reminderSentParam != "undefined" && reminderSentParam == "yes")) {
    //    checkCookie();

    //}




    function HTMLEncode(str) {

        var i = str.length,
            aRet = [];

        while (i--) {
            var iC = str[i].charCodeAt();
            alert(iC);
            if (iC < 65 || iC > 127 || (iC>90 && iC<97)) {
                aRet[i] = '&#'+iC+';';
            } else {
                aRet[i] = str[i];
            }
        }
        return aRet.join('');
    }
</script>


"hello world";

</body>