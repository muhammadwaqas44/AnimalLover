@extends('layout.dashboard.dashboard')

@section('title','Admin-Dashboard')

@section('content')
    <style>
        .container{max-width:1170px; margin:auto;}
        img{ max-width:100%;}
        .inbox_people {
            background: #f8f8f8 none repeat scroll 0 0;
            float: left;
            overflow: hidden;
            width: 40%; border-right:1px solid #c4c4c4;
        }
        .inbox_msg {
            border: 1px solid #c4c4c4;
            clear: both;
            overflow: hidden;
        }
        .top_spac{ margin: 20px 0 0;}


        .recent_heading {float: left; width:40%;}
        .srch_bar {
            display: inline-block;
            text-align: right;
            width: 60%;
        }
        .headind_srch{ padding:10px 29px 10px 20px; overflow:hidden; border-bottom:1px solid #c4c4c4;}

        .recent_heading h4 {
            color: #05728f;
            font-size: 21px;
            margin: auto;
        }
        .srch_bar input{ border:1px solid #cdcdcd; border-width:0 0 1px 0; width:80%; padding:2px 0 4px 6px; background:none;}
        .srch_bar .input-group-addon button {
            background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
            border: medium none;
            padding: 0;
            color: #707070;
            font-size: 18px;
        }
        .srch_bar .input-group-addon { margin: 0 0 0 -27px;}

        .chat_ib h5{ font-size:15px; color:#464646; margin:0 0 8px 0;}
        .chat_ib h5 span{ font-size:13px; float:right;}
        .chat_ib p{ font-size:14px; color:#989898; margin:auto}
        .chat_img {
            float: left;
            width: 11%;
        }
        .chat_ib {
            float: left;
            padding: 0 0 0 15px;
            width: 88%;
        }

        .chat_people{ overflow:hidden; clear:both;}
        .chat_list {
            border-bottom: 1px solid #c4c4c4;
            margin: 0;
            padding: 18px 16px 10px;
        }
        .inbox_chat { height: 550px; overflow-y: scroll;}

        .active_chat{ background:#ebebeb;}

        .incoming_msg_img {
            display: inline-block;
            width: 6%;
        }
        .received_msg {
            display: inline-block;
            padding: 0 0 0 10px;
            vertical-align: top;
            width: 92%;
        }
        .received_withd_msg p {
            background: #ebebeb none repeat scroll 0 0;
            border-radius: 3px;
            color: #646464;
            font-size: 14px;
            margin: 0;
            padding: 5px 10px 5px 12px;
            width: 100%;
        }
        .time_date {
            color: #747474;
            display: block;
            font-size: 12px;
        }
        .received_withd_msg { width: 57%;}
        .mesgs {
            float: left;
            padding: 30px 15px 0 25px;
            width: 60%;
        }

        .sent_msg p {
            background: #05728f none repeat scroll 0 0;
            border-radius: 3px;
            font-size: 14px;
            margin: 0; color:#fff;
            padding: 5px 10px 5px 12px;
            width:100%;
            float: right;
        }
        .outgoing_msg{ overflow:hidden; margin:26px 0 26px;}
        .sent_msg {
            float: right;
            width: 46%;
        }
        .input_msg_write input {
            background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
            border: medium none;
            color: #4c4c4c;
            font-size: 15px;
            min-height: 48px;
            width: 100%;
        }

        .type_msg {border-top: 1px solid #c4c4c4;position: relative;}
        .msg_send_btn {
            background: #05728f none repeat scroll 0 0;
            border: medium none;
            border-radius: 50%;
            color: #fff;
            cursor: pointer;
            font-size: 17px;
            height: 33px;
            position: absolute;
            right: 0;
            top: 11px;
            width: 33px;
        }
        .messaging { padding: 0 0 50px 0;}
        .msg_history {
            height: 516px;
            overflow-y: auto;
        }
    </style>
    <div class="content-page">

        <!-- Start content -->
        <div class="content">

            <div class="container-fluid">

                <div class="row">
                    <div class="col-xl-12">
                        <div class="breadcrumb-holder">
                            <h1 class="main-title float-left">Dashboard <small>(chat)</small></h1>
                            <ol class="breadcrumb float-right">
                                <li class="breadcrumb-item">Home</li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <!-- end row -->
                <div class="row">
                   user id:  {{\Illuminate\Support\Facades\Auth::user()->id}}

                    <div class="col-xl-12">
{{--                        <input type="text" class="message" id="message">--}}
{{--                        <button type="button" id="send">Send</button>--}}
{{--                        <div id="typing">Typing...</div>--}}
{{--                        <div id="chat-messages" style="overflow-y: scroll; height: 100px; ">aaaa</div>--}}
                        <div class="messaging">
                            <div class="inbox_msg">
                                <div class="inbox_people">
                                    <div class="headind_srch">
                                        <div class="recent_heading">
                                            <h4>Recent</h4>
                                        </div>
                                        <div class="srch_bar">
                                            <div class="stylish-input-group">
                                                <input type="text" class="search-bar"  placeholder="Search" id="search_user">
                                                <span class="input-group-addon">
                <button type="button"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                </span> </div>
                                        </div>
                                    </div>
                                    <div class="inbox_chat" id="conversations">

                                    </div>
                                </div>

                                <div class="mesgs">
                                    <div class="msg_history">


{{--                                                <span class="time_date"> 11:01 AM    |    June 9</span>--}}

                                    </div>
                                    <div id="typing">Typing...</div>
                                    <div class="type_msg">
                                        <div class="input_msg_write">
                                            <input type="text" class="write_msg" placeholder="Type a message" id="message" autocomplete="off"/>
                                            <button class="msg_send_btn" type="button" id="send"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

{{--                            <button type="button"  id="chat"> Get chat </button>--}}



                        </div></div>
                    </div>
                </div>
            </div>
            <!-- END container-fluid -->

        </div>
        <!-- END content -->

    </div>
@endsection


@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.3.0/socket.io.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#typing').hide();
            {{--$(window).load(function() {--}}
            {{--    var data = {--}}
            {{--        token: '{{\Illuminate\Support\Facades\Auth::user()->token}}'--}}
            {{--    };--}}
            {{--    var conversation_id;--}}
            {{--    $.blockUI({--}}
            {{--        css: {--}}
            {{--            border: 'none',--}}
            {{--            padding: '15px',--}}
            {{--            backgroundColor: '#000',--}}
            {{--            '-webkit-border-radius': '10px',--}}
            {{--            '-moz-border-radius': '10px',--}}
            {{--            opacity: .5,--}}
            {{--            color: '#fff'--}}
            {{--        }--}}
            {{--    });--}}
            {{--    $.ajax({--}}
            {{--        type: 'POST',--}}
            {{--        url: '{{route('conversations')}}',--}}
            {{--        data: data,--}}
            {{--        success: function(data) {--}}
            {{--            if (data.status == 0) {--}}
            {{--                $.unblockUI();--}}
            {{--                toastr.error(data.message);--}}
            {{--            }--}}
            {{--            if (data.status == 1) {--}}
            {{--                $.unblockUI();--}}
            {{--                if(data.data.length > 0) {--}}
            {{--                    for (i = 0; i < data.data.length; i++) {--}}
            {{--                        var el = data.data[i];--}}
            {{--                        // active_chat--}}
            {{--                        var html = '\--}}
            {{--                    <div class="chat_list">\--}}
            {{--                        <div class="chat_people">\--}}
            {{--                            <div class="chat_img"> \--}}
            {{--                                <img src="' + el.user_pic + '" alt="sunil"> \--}}
            {{--                                <span style="font-size: 0px;visibility: hidden" class="conversation_id">' + el.conversation_id + '</span>\--}}
            {{--                            </div>\--}}
            {{--                            <div class="chat_ib">\--}}
            {{--                                <h5>' + el.user_name + ' <span class="chat_date">' + el.last_message_time + '</span></h5>\--}}
            {{--                                <p> ' + el.last_message + ' </p>\--}}
            {{--                            </div>\--}}
            {{--                        </div>\--}}
            {{--                     </div>\--}}
            {{--                    ';--}}
            {{--                        $('#conversations').append(html);--}}
            {{--                    }--}}
            {{--                }else{--}}
            {{--                    $('#conversations').append('No conversation found');--}}
            {{--                }--}}

            {{--                // $('.chat_list').click(function () {--}}
            {{--                //--}}
            {{--                // });--}}
            {{--            }--}}
            {{--        }--}}
            {{--    });--}}
            {{--});--}}


            var socket;
            // socket = io.connect("http://127.0.0.1:8080");
            socket = io.connect("http://portal.animalloversclick.com:8080");
            socket.on('connected', function(data){
                console.log(data);
                var d = {
                    socket_id: socket.id,
                    user_token: '{{\Illuminate\Support\Facades\Auth::user()->token}}'
                }
                socket.emit('join room', d);
            });
            //--------------------------------------------------------------------------------------------------------------
            {{--$(document).on('click','.chat_list', function(){--}}
            {{--    --}}
            {{--    --}}
            {{--    --}}
            {{--    --}}
            {{--    --}}
            {{--    conversation_id = $(this).find('.conversation_id').text();--}}
            {{--    var data = {--}}
            {{--        conversation_id: conversation_id,--}}
            {{--        token: '{{\Illuminate\Support\Facades\Auth::user()->token}}'--}}
            {{--    };--}}
            {{--    $.blockUI({--}}
            {{--        css: {--}}
            {{--            border: 'none',--}}
            {{--            padding: '15px',--}}
            {{--            backgroundColor: '#000',--}}
            {{--            '-webkit-border-radius': '10px',--}}
            {{--            '-moz-border-radius': '10px',--}}
            {{--            opacity: .5,--}}
            {{--            color: '#fff'--}}
            {{--        }--}}
            {{--    });--}}
            {{--    $.ajax({--}}
            {{--        type: 'POST',--}}
            {{--        url: '{{route('get-chat')}}',--}}
            {{--        data: data,--}}
            {{--        success: function(data) {--}}
            {{--            if (data.status == 0) {--}}
            {{--                // $.unblockUI();--}}
            {{--                // toastr.error(data.message);--}}
            {{--            }--}}
            {{--            if (data.status == 1) {--}}
            {{--                $('.msg_history').empty();--}}
            {{--                $.unblockUI();--}}
            {{--                // console.log(data.data);--}}
            {{--                var html;--}}
            {{--                for (i = 0; i < data.data.chat.length; i++ ){--}}
            {{--                    var el = data.data.chat[i];--}}

            {{--                    if(el.sender == "other"){--}}
            {{--                        html = '\--}}
            {{--                                <div class="incoming_msg">\--}}
            {{--                                    <div class="incoming_msg_img"> <img src="'+ el.sender_pic +'" alt="sunil"> </div>\--}}
            {{--                                    <div class="received_msg">\--}}
            {{--                                       <div class="received_withd_msg">\--}}
            {{--                                            <p>'+ el.message +'</p>\--}}
            {{--                                            <span class="time_date"> '+ el.time+'</span>\--}}
            {{--                                        </div>\--}}
            {{--                                    </div>\--}}
            {{--                                </div>\--}}
            {{--                        ';--}}
            {{--                    }--}}
            {{--                    if(el.sender == "me"){--}}
            {{--                        html = '\--}}
            {{--                            <div class="outgoing_msg">\--}}
            {{--                                <div class="sent_msg">\--}}
            {{--                                    <p>'+ el.message +'</p>\--}}
            {{--                                    <span class="time_date"> '+ el.time+'</span>\--}}
            {{--                                </div>\--}}
            {{--                            </div>\--}}
            {{--                        ';--}}
            {{--                    }--}}
            {{--                    $('.msg_history').append(html);--}}
            {{--                }--}}
            {{--            }--}}
            {{--        }--}}
            {{--    });--}}
            {{--    console.log(conversation_id);--}}
            {{--});--}}

            socket.on('user not found', function(data){
                console.log(data);
            });

            {{--//----------------------------------------------------------------------------------------------------------------}}
            $('#send').click(function () {

                if($('#search_user').val().length == 0){
                    return toastr.error('Please add user id in search bar');
                }
                // console.log($('#search_user').val());
                //
                // return;
                // socket.emit('send', 'data');
                // return;

                const msg = $('#message').val();
                var data = {
                    message: msg,
                    socket_id: socket.id,
                    sender_token: '{{\Illuminate\Support\Facades\Auth::user()->token}}',
                    receiver_id: $('#search_user').val()
                };

                socket.emit('send message', data);
                // $('#message').val('');
                // socket.emit('typing', {receiver_id: 4, typing:false});
            });
            //--------------------------------------------------------------------------------------------------------------
            socket.on('new message', function(data){
                console.log(data);
            });
            //--------------------------------------------------------------------------------------------------------------
            // $("#message").on('change keydown keyup paste input', function(){
            //     if($('#message').val().length != 0){
            //         socket.emit('typing', {receiver_id: 4, typing:true});
            //     }
            //     if($('#message').val().length == 0){
            //         socket.emit('typing', {receiver_id: 4, typing:false});
            //     }
            // });
            //
            // socket.on('typing', function (data) {
            //     if(data){
            //         $('#typing').show();
            //     }else{
            //         $('#typing').hide();
            //     }
            // });
            //

        // });
        });


    </script>
@endsection






