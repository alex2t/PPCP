<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://code.jquery.com/jquery-3.5.0.js"></script>


  <!--Import Google Icon Font-->
  
  <link rel="stylesheet" href="./css/card.css">

  <title>PayPal ACDC</title>
  <?php
    session_start();
    $rootPath = "";
    
    include_once('api/Config/Config.php');
    include_once('api/Helpers/PayPalHelper.php');

    include_once('api/Config/Sample.php');
     $baseUrl = str_replace("index.php", "", URL['current']);
     
    $paypalHelper = new PayPalHelper;
    ?>

</head>




<table border="0" align="center" valign="top" bgcolor="#FFFFFF" style="width: 39%">
     <tr>
       <td colspan="2">
         <div id="paypalCheckoutContainer"></div>
       </td>
     </tr>
     <tr><td colspan="2">&nbsp;</td></tr>
</table>

<div align="center"> or </div>

   <!-- Advanced credit and debit card payments form -->
   <div class="card_container">
     <!--<form id="card-form">

       <label id ="card" for="card-number">Card Number</label><div id="card-number" class="card_field"></div>
       <div>
         <label id="date" for="expiration-date">Expiration Date</label>
         <div id="expiration-date" class="card_field"></div>
       </div>
       <div>
         <label id="check" for="cvv">CVV</label><div id="cvv" class="card_field"></div>
       <br><br>
       <button value="submit" id="submit" class="btn">Pay</button>
     </form>-->

     <form id="card-form">
  <div class="panel">
    <header class="panel__header">
      <h1>Card Payment</h1>
    </header>
    <div class="panel__content">

        <label id ="card" class="card_field--label" for="card-number"><span class="icon">
         <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg></span> Card Number 
        </label>
        <div id="card-number" class="card_field"></div>
         
     
      <div class="textfield--float-label">
        <!-- Begin hosted fields section -->
        <label id="date" class="card_field--label" for="expiration-date">
           <span class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/></svg>
            </span>
        Expiration Date</label>
        <div id="expiration-date" class="card_field"></div>
      </div>
      <div class="textfield--float-label">
        <label id="check" class="card_field--label" for="cvv">
          <span class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
            </span>
        CVV</label>
        <div id="cvv" class="card_field"></div>
      </div>
      
    </div><!-- panel_content -->
    <footer class="panel__footer">
      <button id="submit" class="pay-button">Pay</button>
    </footer>
  </div>
</form>
<div id="payments-sdk__contingency-lightbox"></div>
   </div>

   <p id="message"></p>

   <div id='container'> </div>

   <div class="box">
        
    </div>


    <script src="https://www.paypal.com/sdk/js?components=buttons,hosted-fields&client-id=<?= PAYPAL_CREDENTIALS[PAYPAL_ENVIRONMENT]['client_id'] ?>&intent=capture&vault=false&commit=true&currency=EUR"data-client-token="<?php echo $paypalHelper->getClientToken(); ?>"></script>

<script type="text/javascript">

let clientToken = "<?php echo $paypalHelper->getClientToken(); ?>";
console.log('ClientToken: '+ clientToken);
console.log('[paypal.HostedFields.isEligible()]: ', paypal.HostedFields.isEligible());


    paypal.Buttons({

        // Set your environment
        env: '<?= PAYPAL_ENVIRONMENT ?>',

        // Set style of buttons
        style: {
            layout: 'vertical',   // horizontal | vertical
            size:   'responsive',   // medium | large | responsive
            shape:  'pill',         // pill | rect
            color:  'gold',         // gold | blue | silver | black,
            fundingicons: false,    // true | false,
            tagline: false          // true | false,
        },

        // Wait for the PayPal button to be clicked
        createOrder: function() {
            
            return fetch(
                '<?= $rootPath.URL['services']['orderCreate']?>',
                {
                    method: 'POST',
                   
                }
            ).then(function(response) {
                return response.json();
            }).then(function(resJson) {
                console.log('Order ID: '+ resJson.data.id);
                return resJson.data.id;
            });
        },

        // Wait for the payment to be authorized by the customer
        onApprove: function(data, actions) {
            return fetch(
                '<?= $rootPath.URL['services']['orderCapture'] ?>',
                {
                    method: 'POST',
                    body: JSON.stringify({
                    orderID: data.orderID
                    })
                }
            ).then(function(res) {
                return res.json();
            }).then(function(res) {
                console.log(res);
            });
        }

    }).render('#paypalCheckoutContainer');

if (paypal.HostedFields.isEligible()) {

// Renders card fields
    paypal.HostedFields.render({
    // Call your server to set up the transaction
    createOrder: function () {
        return fetch(
            '<?= $rootPath.URL['services']['orderCreate'] ?>', 
            {
                method: 'post'
            }
        ).then(function(response) {
        return response.json();
        }).then(function(resJson) {
            console.log('Order ID: '+ resJson.data.id);
            return resJson.data.id;
                
        });
    },

    styles: {
        '.valid': {
        'color': 'green'
        },
        '.invalid': {
        'color': 'red'
        }
    },

    fields: {
        number: {
        selector: "#card-number",
        placeholder: "4111 1111 1111 1111"
        },
        cvv: {
        selector: "#cvv",
        placeholder: "123"
        },
        expirationDate: {
        selector: "#expiration-date",
        placeholder: "MM/YY"
        }
    }
    }).then(function (hostedFieldsInstance){
        
        hostedFieldsInstance.on('blur', function (event) {
            var state= hostedFieldsInstance.getState();
            //number expirationDate  cvv
            if (event.emittedBy == "number" ){
                
                if (state.fields.number.isValid != true){
                    document.getElementById("card").style.color = "Red";
                    document.getElementById('card').innerHTML = "Card number is invalid";
                     console.log("card number is invalid");
                }else if (state.fields.number.isValid === true){
                    document.getElementById("message").innerHTML ="";
                    if (state.cards[0].niceType !=""){
                        console.log(state.cards[0].niceType);
                        document.getElementById("card").style.color = "Blue";
                        document.getElementById('card').innerHTML = state.cards[0].niceType
                    }
                    else{
                        console.log(" valid card");
                    }
                }
            }
            if (event.emittedBy == "expirationDate" ){
                if (state.fields.expirationDate.isValid != true){
                    document.getElementById("date").style.color = "Red";
                    document.getElementById('date').innerHTML = "The expiration Date is invalid";
                    console.log("The expiration Date is invalid");
                }else{
                    document.getElementById("message").innerHTML ="";
                    document.getElementById('date').innerHTML = "Expiration Date";
                    document.getElementById("date").style.color = "black";
                    console.log("valid date");
                }               
            }
            if (event.emittedBy == "cvv" ){
                if(state.fields.cvv.isValid != true){
                    document.getElementById("check").style.color = "Red";
                    document.getElementById('check').innerHTML = "The cvv is invalid";
                    console.log("The cvv is invalid ");
                }else{
                    document.getElementById("message").innerHTML ="";
                    document.getElementById('check').innerHTML = "CVV";
                    document.getElementById("check").style.color = "black";
                    console.log("Valid CVV");
                }
            }
        });

        document.querySelector('#card-form').addEventListener('submit', event => {

          event.preventDefault();
          var state= hostedFieldsInstance.getState();
          var obj= JSON.stringify(state); 
          console.log(" the state of the fields " + obj);

          var formValid = Object.keys(state.fields).every(function (key) {
            return state.fields[key].isValid;   // return true or false
          });
          var obj= JSON.stringify(formValid); 
          console.log(" the formValid object " + obj);
        if(formValid){
            hostedFieldsInstance.submit({contingencies: ['SCA_ALWAYS']}).then(payload => {
            
               
                console.log("payload.liabilityShifted " + payload.liabilityShifted);
                console.log("payload. " + payload);
                
                return fetch(
                '<?= $rootPath.URL['services']['verification3d'] ?>',
                    {
                        method: 'POST',
                        body: JSON.stringify({
                        orderID: payload.orderId
                        })
                    }
                ).then(function(response) {
                    // console.log("vefore "+  output(res))
                    return response.json();
                }).then(function(response) {
                    // console.log("return "+ output(two));
                    console.log(JSON.stringify(response));
                    var arr= Object.getOwnPropertyNames(response);
                    console.log('arr ' + arr);
                    console.log('arr lenght ' + arr.length);
                    console.log('return ' + Object.getOwnPropertyNames(response));
                    console.log(response.capture);
                    console.log(response.verification);
                    
                });
            }); // end of hostedFieldsInstance

        }else{
            console.log(" some errors are present");
            var arr =[];
            if (state.fields.number.isValid != true){
            arr.push("the cards numbers");
            }
            if(state.fields.cvv.isValid != true){
                arr.push("the cvv  ");
            }
            if (state.fields.expirationDate.isValid != true){
                arr.push("the expiration date")
            }
       }
       if ( typeof arr != 'undefined' && arr instanceof Array ){
            if(arr.length>1){
                arr.push("are invalid");
            }else{
                arr.push("is invalid");
            }
            let message= arr.toString();
            document.getElementById("message").innerHTML = message;
       }
      });
    })

} else {
    // Hides card fields if the merchant isn't eligible
    document.querySelector("#card-form").style = 'display: none';
}

    function output(obj){
        const keys = Object.keys(obj);
        keys.forEach((key, index) => {
            console.log(`${key}: ${obj[key]}`);
        });
    }


    
</script>