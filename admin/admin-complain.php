<?php
  require_once '../core/init.php';
  if (!isIsLoggedIn()){
      Session::flash('warning', 'You need to login to access that page!');
      Redirect::to('admin-login');
  }
  if (!hasPermissionSuper()){
      Session::flash('denied', 'You do not have permission to access that page!');
      Redirect::to('admin-dashboard');
  }
  $admin = new Admin();
  $useremail = $admin->data()->admin_email;
  $uniqueid = $admin->data()->admin_uniqueid;
  if (otpCheck()) {
    Session::flash('emailVerify', 'Please verify your email!', 'warning');
    Redirect::to('admin-verify');
  }elseif(isOTPset($uniqueid)){
    Redirect::to('admin-otp');
  }
  require APPROOT . '/includes/adminhead.php';
  require APPROOT . '/includes/adminnav.php';

  $general = new General();





 ?>
<style media="screen">
  .form-control{
    color: #fff;
  }
  option{
    color: #fff;
    background: #000;
  }

</style>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <!-- table -->
      <div class="col-lg-8 col-md-12">
        <div class="card">
          <div class="card-header card-header-primary">
            <h4 class="card-title">Complains</h4>
            <p class="card-category">List Of unresolved complains</p>
          </div>
          <div class="card-body table-responsive"  id="complain">

          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-12">
        <div class="card">
          <div class="card-header card-header-tabs card-header-warning">
            <div class="nav-tabs-navigation">
              <div class="nav-tabs-wrapper">
                <span class="nav-tabs-title">Form:</span>
                <ul class="nav nav-tabs" data-tabs="tabs">
                  <li class="nav-item">
                    <a class="nav-link active" href="#profile" data-toggle="tab">
                      <i class="material-icons fa fa-user-plus fa-lg"></i>Resolution Status
                      <div class="ripple-container"></div>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="tab-content">
              <div class="tab-pane active">
                <hr>
                <form class="form" action="#" method="post" id="resolvedForm">
                  <div class="form-group">
                    <label for="student">Student: <sup class="text-danger
                      ">*</sup></label>
                      <select class="form-control" name="student" id="student">
                        <option value="">Select Student</option>
                        <?php
                          $compl = $general->grabComplainInput();

                          foreach ($compl as $cp) {
                          $stud = new User($cp->stu_session_id);
                          echo '<option value="'.$cp->stu_session_id.'">'.$stud->data()->stud_regNo.'</option>';
                          }
                         ?>

                      </select>
                  </div>
                  <div class="form-group">
                    <label for="complain">Compalin: <sup class="text-danger
                      ">*</sup></label>
                      <select class="form-control" name="complain" id="complain">
                        <option value="">Select Complain</option>
                        <?php
                          $compl = $general->grabComplainInput();
                          foreach ($compl as $cp) {
                          echo '<option value="'.$cp->id.'">'.$cp->complain.'</option>';
                          }
                         ?>

                      </select>
                  </div>
                  <div class="form-group">
                    <label for="status">Status: <sup class="text-danger
                      ">*</sup></label>
                      <select class="form-control" name="status" id="status">
                        <option value="">Select Status</option>
                        <option value="in_progress">In Progress</option>
                        <option value="on_hold">On Hold</option>
                        <option value="yes">Resolved</option>

                      </select>
                  </div>
                  <div class="form-group">
                    <button type="button" name="update" id="updateBtn" class="btn btn-info btn-block">Update</button>
                    <div class="clear-fix"></div>
                    <div id="showError"></div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
<!-- Modal view detail complain -->
<div class="modal fade" id="CPDetail" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Complain Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>

      </div>
      <div class="modal-body text-primary table-responsive" id="grabcomplain">

      </div>
      <div class="modal-footer">
           <hr class="invisible">
        <div id="showResponse"></div>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- end detail -->

<!-- edit modal complain -->
<div class="modal fade" id="CPEdit" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Complain Edit</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>

      </div>
      <div class="modal-body bg-dark text-primary table-responsive" id="grabcomplainEdit">

      </div>
      <div class="modal-footer">
           <hr class="invisible">
        <div id="showResponse"></div>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- end edit -->

<?php
  require APPROOT . '/includes/adminfooter.php';
 ?>
<script type="text/javascript">
  $(document).ready(function(){

     //assign students
  $('body').on('click', '.CPDetailIcon', function(e){
    e.preventDefault();
    complain_id = $(this).attr('d-id');
    $.ajax({
      url: 'scripts/super-process.php',
      method: 'post',
      data: {complain_id:complain_id},
      success:function(data){
        $('#grabcomplain').html(data);
      }
    })
  });
  $('body').on('click', '.CPEditIcon', function(e){
    e.preventDefault();
    complain_ide = $(this).attr('e-id');
    $.ajax({
      url: 'scripts/super-process.php',
      method: 'post',
      data: {complain_ide:complain_ide},
      success:function(data){
        $('#grabcomplainEdit').html(data);
      }
    })
  });

 $('body').on('click', '.editcomplainBtn', function(e){
    e.preventDefault();
    $.ajax({
      url: 'scripts/super-process.php',
      method: 'post',
      data: $('#editcomplainForm').serialize()+'&action=editcomplain',
      success:function(data){
        if (data==='success') {
          fetchcomplain();
         $('#showMsg').html(data);
        }else{
          $('#showMsg').html(data);
        }

     }
    })
  })


    $('#updateBtn').click(function(e){
      e.preventDefault();
      $.ajax({
        url:'scripts/super-process.php',
        method:'post',
        data:$('#resolvedForm').serialize()+'&action=resolveComplain',
        beforeSend:function(){
          $('#updateBtn').html('Updating...');
        },
        success:function(response){
          if (response==='success') {
              $('#resolvedForm')[0].reset();
              $('#showError').html('<div id="" class="alert alert-success alert-dismissible"> <button type="button" class="close" data-dismiss="alert"> &times;</button><i class="fa fa-check"></i>&nbsp; <span>Success!</span></div>');
              fetchcomplain();
          }else{
            $('#showError').html(response);
            // setTimeout(function(){
            //     $('#showError').html('');
            // },10000);

          }
        },
        complete:function(){
          $('#updateBtn').html('Update');
        }
      })
    });

    //fetch admin

    fetchcomplain();
    function fetchcomplain(){
      action = "fetchComplain";
      $.ajax({
        url:'scripts/super-process.php',
        method:'post',
        data:{action:action},
        success:function(response){
          $('#complain').html(response);
          $('#showcomplain').DataTable({
              "paging": true,
              "lengthChange": true,
              "searching": true,
              "ordering": true,
              "order": [0,'desc'],
              "info": true,
              "hover": false,
              "autoWidth": true,
              "responsive": false,
              "lengthMenu": [[10,10, 25, 50, -1], [10, 25, 50, "All"]]
          });
        }
      })
    }

    // setInterval(function(){
    //   fetchAdminData();
    // },1000)

  })
</script>
