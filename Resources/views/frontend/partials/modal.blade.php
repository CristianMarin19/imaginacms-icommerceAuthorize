<div class="col-10 text-center">

    <p>
        Lorem ipsum dolor sit amet consectetur adipisicing elit. 
        Minus dolorem maiores sint expedita perspiciatis voluptatibus vel nemo aliquid dignissimos tempore 
        iste, odio esse adipisci soluta quas ullam consequatur delectus! Inventore.
    </p>

    <form id="paymentForm">
        <input type="hidden" name="dataValue" id="dataValue"/>
        <input type="hidden" name="dataDescriptor" id="dataDescriptor" />
        <button type="button"
            class="AcceptUI btn btn-success btn-lg text-white "
            data-billingAddressOptions='{"show":true, "required":false}' 
            data-apiLoginID="{{$apiLogin}}"
        data-clientKey="{{$clientKey}}"
            data-acceptUIFormBtnTxt="Submit" 
            data-acceptUIFormHeaderTxt="Card Information" 
            data-responseHandler="responseHandler">CLICK HERE TO PAY
        </button>
    </form>

</div>

@section('scripts')
    @parent
    <script type="text/javascript"
    src="{{$acceptJS}}"
    charset="utf-8">
    </script>

    <script type="text/javascript">
      
       
        function responseHandler(response) {
            if (response.messages.resultCode === "Error") {

                alert("Ha ocurrido un error, vuelve a intentarlo");

                var i = 0;
                while (i < response.messages.message.length) {
                    console.log(
                        response.messages.message[i].code + ": " +
                        response.messages.message[i].text
                    );
                    i = i + 1;
                }
            } else {
                console.log("Funciono");
                paymentFormUpdate(response.opaqueData);
            }
        }
        
        
        function paymentFormUpdate(opaqueData) {
           
            var url = "{{url('/icommerceauthorize/send')}}";
            window.location.href = url+"/"+opaqueData.dataValue+"/"+opaqueData.dataDescriptor;

        }

    
    </script>
  

@stop
