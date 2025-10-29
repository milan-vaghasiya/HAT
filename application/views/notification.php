<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Notification Testing</h4>
                            </div>
                            <div class="col-md-6">
                                
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <form id="testNotification">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label for="notificationTitle">Notification Title</label>
                                        <input type="text" name="notificationTitle" id="notificationTitle" class="form-control" value="Test Notification">
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <label for="notificationTitle">Notification Message</label>
                                        <input type="text" name="notificationMsg" id="notificationMsg" class="form-control" value="Notification test successfull.">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="store('testNotification','notification/send');" ><i class="fas fa-paper-plane"></i> Send</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
