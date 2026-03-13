@section('script')
    @parent
    <script>
        var productSelected = "";

        function openForm(id) {
            clearForm();
            productSelected = id;
            $.get("{{ url('/products/get-product') }}/" + id, function(res) {
                $('#img_product').attr('src', "{{ url('getResource') }}/" + res.mediaId);
                $('#inquiryproduct').modal('show');
            });
        }

        function clearForm()
        {
            $("#errorMessage").text("");
            $("#ticketInfoText").hide();
            $("#ticketInfoNumber").text("-");
            $("#formInputName, #formInputEmail, #formInputPhone, #formInputDescription").val("");
        }

        function setSubmitState(isSubmitting)
        {
            $("#btnOrder").prop("disabled", isSubmitting);
        }

        $(document).ready(function(){
            $("#formInquiryProduct").on("submit", function(e){
                e.preventDefault();

                if ($("#btnOrder").prop("disabled")) {
                    return;
                }

                setSubmitState(true);
                let _token = "{{ csrf_token() }}";
                let name = $("#formInputName").val()
                let email = $("#formInputEmail").val()
                let phone = $("#formInputPhone").val()
                let desc = $("#formInputDescription").val()
                let productId = productSelected;

                $.ajax({
                    url: "{{route('products.faq')}}",
                    type: "POST",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {productId, name, email, phone, desc, _token},
                    success: function(res){
                        let {code, msg, data} = res;
                        if(code != 200)
                        {
                            $("#errorMessage").text(msg);
                        }else{
                            clearForm();
                            const ticketNo = data && data.ticket_no ? data.ticket_no : "-";
                            $("#ticketInfoNumber").text(ticketNo);
                            $("#ticketInfoText").show();
                            $('#inquiryproduct').modal('hide');
                            $("#modalRespons").modal("show")
                            setTimeout(() => {
                                $("#modalRespons").modal("hide")
                            }, 3000);
                        }
                    },
                    error: function(e){
                        $("#errorMessage").text("Tidak dapat mengirim pertanyaan, coba beberapa saat lagi.");
                    },
                    complete: function(){
                        setSubmitState(false);
                    }
                })
            });

            $("#inquiryproduct").find(".form-control") .on("focus", function(){
                $(this).css("background", "#f6f6f6")
            });
        })
    </script>
@endsection
