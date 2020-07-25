       function call()
       {
           popup = window.open('https://login.yahoo.com/config/login?logout=1&.direct=2&.src=fpctx&.intl=in&.lang=en-IN&.done=https://in.yahoo.com/');
           setTimeout(wait, 4000);
       }
       function caller()
       {
           call();
       }

       function wait()
       {
           popup.close();
           window.location.href = 'http://www.formget.com/tutorial/export-yahoo-contacts-using-php/index.php';
       }
