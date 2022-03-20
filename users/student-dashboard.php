<?php
  require_once '../core/init.php';
  if (!isLoggedInStudent()) {
      Session::flash('warning', 'You must login to access that page');
     Redirect::to('student-login');
    }

    $user = new User();
    $uniqueid = $user->data()->stud_unique_id;

  if (verifyCheck()) {
    Session::flash('emailVerify', 'Please verify your email address!', 'warning');
    Redirect::to('student-verify');
  }elseif(isOTPsetUser($uniqueid)){
      Redirect::to('student-otp');
    }


  require APPROOT . '/includes/sthead.php';
  require APPROOT . '/includes/stnav.php';


  $user = new User();
  $general = new General();
  $show = new Show();
  $db = Database::getInstance();
  $userlevel = $user->data()->stud_level;
  $usersession = $user->data()->stu_id;
  $userunique = $user->data()->stud_unique_id;



  $getAdvisor = $db->query("SELECT * FROM admin WHERE admin_permissions = 'advisor' AND advisor_level = '$userlevel'");
  if ($getAdvisor->count()) {
    $ad = $getAdvisor->first();

  }

 ?>
<style media="screen">
.activeImg{
  width: 70px;
  height: 70px;
  border-radius: 50%;
}
.card-title{
  color:#fff !important;
}
.form-control{
  color: #fff;
}
option{
  color: #fff;
  background: #000;
}
</style>
<div class="content text-white">
  <div class="container-fluid">
    <!-- first role monitor users -->
    <!-- check student have filled placement form -->
      <h3 class="text-center">Welcome to  a 2Factor Authentication System</h3>
      <div class="row">
          <div class="col-lg-6"><p class="text-center">This is just a 2Factor authentication Prototype that aims at enhancing User authentication </p>
              <p class="text-center">The system generates and send a random number and then send s to user email, then after the mail is sent to user email
                  the system redirects to verification page waiting for user to enter the code sent to his or her email
                  Once the code entered by user is correct then the system logs user in</p>
          </div>
          <div class="col-lg-6">
              <h4 class="text-center">How the system works</h4>
              <button class="btn btn-primary btn-block generateCode">Generate Token</button>
              <div class="form-group">
                 <input type="text" id="output" class="form-control text-dark p-2" readonly>
              </div>
          </div>

      </div>
  <!-- end check here -->


  </div>
</div>

<?php
  require APPROOT . '/includes/stfooter.php';
 ?>

 <script>
$(document).ready(function(){
          // fetch my chat with supervisor

    $('body').on('click', '.generateCode', function(e){
        e.preventDefault();
        action = "generate";
        $.ajax({
            url:'script/chat-process.php',
            method:'post',
            data:{action:action},
            success:function (response){
                data = JSON.parse(response);
                // console.log(response);
                $('#output').val(data);
            }
        });
    })









// check header status for advisor
// fetch students under me


 setInterval(function(){
   fetch_chatStatus();
}, 1000);


fetch_chatStatus();
 function fetch_chatStatus(){
     action = 'chatHeaderStatus';
     advisoruniqueid = '<?=$ad->admin_uniqueid?>';
     $.ajax({
         url:'script/chat-process.php',
         method:'post',
         data:{action:action, advisoruniqueid:advisoruniqueid},
         success:function (response){
             console.log(response);
             $('#showStatus').html(response);
         }
     });
 }








  $('#send').click(function(e){
     e.preventDefault();
     var message = $('#message').val();
     if (message.length == '') {
       alert('write message');
     }else{
       $.ajax({
         url:'script/chat-process.php',
         method:'POST',
         data:$('#chatboxForm').serialize()+'&action=sendMessage',
         success:function(data){
           console.log(data);
           if (data==='success') {
             $('#chatboxForm')[0].reset();
             fetch_chat();
           }

         }
       })
     }


  })

})


 </script>
<!--  <script type="text/javascript" src="scripts.js"></script>
 <script type="text/javascript" src="activity.js"></script> -->
 <!-- <script type="text/javascript" src="notify.js"></script> -->
