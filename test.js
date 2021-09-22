
console.log('[paypal.HostedFields.isEligible()]: ', paypal.HostedFields.isEligible());
let clientToken = "<?php echo $client_token ?>";
console.log('ClientToken: '+ clientToken);
console.log('ClientId: ', "<?php echo $clientId; ?>");
// Check if card fields are eligible to render for the buyer's country. The card fields are not eligible in all countries where buyers are located.
  if (paypal.HostedFields.isEligible() === true) {
    paypal.HostedFields.render({
        paymentsSDK: true,
      createOrder: function (data, actions) {
        return fetch('create_payment.php', {
            method: 'POST'
        }).then(function(res) {  
            return res.json();

        }).then(function(data) {
          console.log(data);
          var data =JSON.parse(data);
            return data.id;
            
        }).catch(function(error) {
            console.log('Request failed', error)
        });
      },
      styles: {
        'input': {
            'color': '#3A3A3A',
            'transition': 'color 160ms linear',
            '-webkit-transition': 'color 160ms linear'
        },
        ':focus': {
        'color': '#4028D7'
        },
        '.invalid': {
         'color': '#ca355c'
        },
        '.required': {
         'color': '#00ffab'
        }

      },
      fields: {
          number: {
              selector: '#card-number',
              placeholder: 'Credit Card Number',
          },
          cvv: {
              selector: '#cvv',
              placeholder: 'CVV',
          },
          expirationDate: {
              selector: '#expiration-date',
              placeholder: 'MM/YYYY',
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

        document.querySelector('#my-sample-form').addEventListener('submit', event => {
          console.log('clicked');
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
           hostedFieldsInstance.submit({contingencies: ['3D_SECURE']}).then(payload => {
          
             let OrderId =JSON.stringify(payload.orderId);
             console.log("test the payload "+ OrderId);
             console.log("payload.liabilityShifted " + payload.liabilityShifted);
            
            return fetch('execute_payment.php' , {
            method: 'POST',
            headers: {"Content-Type": "application/json"},
            body: OrderId
            }
          ).then(function(sec) { 
           
            return sec.json();

        })/*.then(function(obj) {  
            return JSON.parse(obj);

        })*/.then(function(finale){
            console.log(finale);
        }).catch(function(error) {
            console.log('2nd Request failed', error);
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
}  // end 

  
//merchand id : N94HPK83QS2S2
