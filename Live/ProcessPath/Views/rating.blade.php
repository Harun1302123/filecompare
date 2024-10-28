<style>
    .d-none{
        display: none;
    }
    .checked{
        color:#DAC16C;
    }
    .checked-border{
        border:2px solid green !important;
    }
    .pointer{
        cursor: pointer;
    }

</style>

<div class="col-md-12">
    <form method="post" id="feedbackId">
        <div class="row">
            <div class="modal fade" style="min-height: 400px;min-width: 500px;" id="rating" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content" style="width: 50%;margin: 0 auto;">
                        <div class="modal-header">
                            <button type="button" id="closebtn" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" style="font-weight: bold">&times;</span>
                            </button>
                            <h5 class="modal-title" id="exampleModalLabel" style="margin-top: 12px;">
                            Your comments is not visible to desk officials, however it motivate us to carry out the activities for future improvement of service delivery
                            </h5>

                        </div>
                        <div class="modal-body" >
                            <div id="messageSection" class="hidden">
                                <div class="alert alert-success" role="alert" style="margin-bottom: 5px;text-align: center"><b>Thank you for your feedback.</b></div>
                            </div>
                            <div id="messageSectionExist" class="hidden">
                                <div class="alert alert-danger" role="alert" style="margin-bottom: 5px;text-align: center"><b>Already submitted feedback for this application.</b></div>
                            </div>
                            <div id="feedbackSection" class="">
                                <strong>How was your experience of this service? <span class="required-star"></span></strong>
                                <div class="row mt-2 mb-2">
                                    <input type="hidden" id="process_id">
                                    <div class="col-md-12">
                                        <label for="rate1" title="Very poor"  class=" starlabel pointer" style="cursor:pointer;font-size: 50px;width: 72px" id="star1"  onclick="add(1)" >
                                            <img src="/assets/images/Feedbackimage/horrible.png" style="width: 100%" id="image_11"> </label>
                                        <input type="radio"  class="d-none" name="rate"  id="rate1" value="1">
                                        <label for="rate2" title="Poor" class="starlabel pointer" style="cursor:pointer;font-size: 50px;width: 72px" id="star2"  onclick="add(2)">
                                            <img src="/assets/images/Feedbackimage/poor.png" style="width: 100%"  id="image_2"> </label>
                                        <input  type="radio" class="d-none" name="rate" id="rate2" value="2">
                                        <label for="rate3" title="Average"  class=" starlabel pointer" style="cursor:pointer;font-size: 50px;width: 72px" id="star3"  onclick="add(3)">
                                            <img src="/assets/images/Feedbackimage/averge.png" style="width: 100%" id="image_3"></label>
                                        <input type="radio" class="d-none" name="rate" id="rate3" value="3">
                                        <label for="rate4" title="Satisfied"  class=" starlabel pointer" style="cursor:pointer;font-size: 50px;width: 72px" id="star4"  onclick="add(4)">
                                            <img src="/assets/images/Feedbackimage/good.png"  style="width: 100%" id="image_4"></label>
                                        <input type="radio" class="d-none" name="rate" id="rate4" value="4">
                                        <label for="rate5" title="Strongly satisfied"  class=" starlabel pointer" style="cursor:pointer;font-size: 50px;width: 72px" id="star5"  onclick="add(5)">
                                            <img src="/assets/images/Feedbackimage/excellent.png" style="width: 100%"  id="image_5"></label>
                                        <input type="radio" class="d-none" name="rate" id="rate5" value="5">
                                    </div>
                                    <div class="col-md-2"></div>
                                </div>
                                <br>

                                <div class="row">
                                    <div class="col-md-12">
                                        <strong>Comment(if any):</strong>
                                        <textarea name="feedback_answer" id="feedback_answer" class="form-control input-sm required" placeholder="Please fill your answer" rows="2" cols="1"></textarea>
                                    </div>
                                </div>

                                <div class="row float-right mt-3">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-info float-right" style="float: right;margin-top: 10px;
}">Submit &nbsp;<i class="fas fa-chevron-right"></i></button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{--<div class="modal fade" style="min-height: 400px;min-width: 500px;" id="rating_after" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">--}}
                {{--<div class="modal-dialog modal-lg" role="document">--}}
                    {{--<div class="modal-content" style="width: 50%;margin: 0 auto;">--}}
                        {{--<div class="modal-header">--}}
                            {{--<h5 class="modal-title" id="exampleModalLabel">Please give your feedback to make our service more--}}
                                {{--user friendly</h5>--}}
                            {{--<button type="button" id="closebtn" class="close" data-dismiss="modal" aria-label="Close">--}}
                                {{--<span aria-hidden="true">&times;</span>--}}
                            {{--</button>--}}
                        {{--</div>--}}
                        {{--<div class="modal-body">--}}
                            {{--<div class="alert alert-success" role="alert" style="margin-bottom: 5px;text-align: center"><b>Thank you for your feedback.</b></div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}


        </div>
    </form>
</div>

<script type="text/javascript">
    function add(sno){
        var cur=document.getElementById("image_"+sno);
        console.log(cur)
        if(sno == 1){
            $('#image_11').attr("src",  "/assets/images/Feedbackimage/_horrible.png");
            $('#image_2').attr("src",  "/assets/images/Feedbackimage/poor.png");
            $('#image_3').attr("src",  "/assets/images/Feedbackimage/averge.png");
            $('#image_4').attr("src",  "/assets/images/Feedbackimage/good.png");
            $('#image_5').attr("src",  "/assets/images/Feedbackimage/excellent.png");
        }else if(sno == 2){
            cur.src= "/assets/images/Feedbackimage/_poor.png";
            $('#image_11').attr("src",  "/assets/images/Feedbackimage/horrible.png");
            $('#image_3').attr("src",  "/assets/images/Feedbackimage/averge.png");
            $('#image_4').attr("src",  "/assets/images/Feedbackimage/good.png");
            $('#image_5').attr("src",  "/assets/images/Feedbackimage/excellent.png");
        }else if(sno == 3){
            cur.src= "/assets/images/Feedbackimage/_averge.png";
            $('#image_11').attr("src",  "/assets/images/Feedbackimage/horrible.png");
            $('#image_2').attr("src",  "/assets/images/Feedbackimage/poor.png");
            $('#image_4').attr("src",  "/assets/images/Feedbackimage/good.png");
            $('#image_5').attr("src",  "/assets/images/Feedbackimage/excellent.png");
        }else if(sno == 4){
            cur.src= "/assets/images/Feedbackimage/_good.png";
            $('#image_11').attr("src",  "/assets/images/Feedbackimage/horrible.png");
            $('#image_2').attr("src",  "/assets/images/Feedbackimage/poor.png");
            $('#image_3').attr("src",  "/assets/images/Feedbackimage/averge.png");
            $('#image_5').attr("src",  "/assets/images/Feedbackimage/excellent.png");
        }else if(sno == 5){
            cur.src= "/assets/images/Feedbackimage/_excellent.png";
            $('#image_11').attr("src",  "/assets/images/Feedbackimage/horrible.png");
            $('#image_2').attr("src",  "/assets/images/Feedbackimage/poor.png");
            $('#image_3').attr("src",  "/assets/images/Feedbackimage/averge.png");
            $('#image_4').attr("src",  "/assets/images/Feedbackimage/good.png");
        }

        // var cur=document.getElementById("star"+sno);
        // cur.className="fa fa-smile checked";
        // cur.className="/assets/images/Feedbackimage/_horrible.png";
       /* for (var i=1;i<=sno;i++){
            var cur=document.getElementById("star"+i)
            if(cur.className=="fa fa-smile")
            {
                cur.className="fa fa-smile checked";
            }
        }*/
    }

    /*function changeborder(sno){
        $('.feedbacktype').find('label').removeClass('checked-border');
        $('#categorylabel'+sno).addClass('checked-border');
    }*/

    function rating(e) {
        $('#process_id').val(e);

        $.ajax({
            url: '/dashboard/feedback/check-already-exist',
            type: "POST",
            data: {
                process_id: e,
            },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function (response) {
                if (response.responseCode==1){
                    console.log(response.responseCode);
                    $('#feedbackSection').addClass('hidden');
                    $('#messageSectionExist').removeClass('hidden');
                }else{
                    $('#feedbackSection').removeClass('hidden');
                    $('#messageSectionExist').addClass('hidden');
                }

                $('#image_11').attr("src",  "/assets/images/Feedbackimage/horrible.png");
                $('#image_2').attr("src",  "/assets/images/Feedbackimage/poor.png");
                $('#image_3').attr("src",  "/assets/images/Feedbackimage/averge.png");
                $('#image_4').attr("src",  "/assets/images/Feedbackimage/good.png");
                $('#image_5').attr("src",  "/assets/images/Feedbackimage/excellent.png");

                $('#rating').modal('show')
            },

            error: function (response) {
                alert('Something Goes Wrong');
            }
        });


    }
    $(document).ready(function(){
        $("#feedbackId").submit(function(e){
            e.preventDefault();
            var ratevalue = $("input[name='rate']:checked").val();
            var process_id = $('#process_id').val();
            if (ratevalue =="" || ratevalue==null){
                alert('Pleae select feedback.');
                return false;
            }else{
                /*var feedback_type = $("input[name='category']:checked").val();*/
                var feedback_content = $('#feedback_answer').val();
                $.ajax({
                    url: '/dashboard/feedback/store',
                    type: "POST",
                    data: {
                        ratevalue: ratevalue,
                        process_id: process_id,
                        feedback_content: feedback_content
                    },
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.responseCode==1){
                            $('#feedbackSection').addClass('hidden')
                            $('#messageSection').removeClass('hidden')
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        }
                    },
                    error: function (response) {
                        alert('Something Goes Wrong');
                    }
                });

            }

        });

        $('#closebtn').click(function () {
            $('#messageSection').addClass('hidden')
            setTimeout(function(){
                $('#feedback_answer').val('');
                add(0);
                $('#feedbackSection').removeClass('hidden')
                }, 1000)
        })

        // $('#rating').on('hidden.bs.modal', function () {
        //     location.reload();
        // })

    });
</script>