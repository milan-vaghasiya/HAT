
<div class="appHeader bg-primary text-light">
	<div class="left">
		<a href="#" class="headerButton" data-bs-toggle="modal" data-bs-target="#sidebarPanel">
			<ion-icon name="menu-outline"></ion-icon>
		</a>
	</div>
	<div class="pageTitle">
		<!--img src="<?=base_url();?>assets/img/logo.png" alt="logo" class="logo">-->
		<?=$companyName?>
	</div>
	<div class="right">
		<!--<a href="app-notifications.html" class="headerButton">
			<ion-icon class="icon" name="notifications-outline"></ion-icon>
			<span class="badge badge-danger">4</span>
		</a>-->
		<!-- <a class="headerButton" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#change-psw">
			<ion-icon name="key-outline"></ion-icon>
		</a> -->
		<a href="<?=base_url('app/login/logout')?>" class="headerButton">
			<i class="fa fa-power-off"></i>
		</a>
	</div>
</div>
<div class="modal fade" id="change-psw" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">

	<div class="modal-dialog" role="document">
		<div class="modal-content animated slideDown">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel1">Change Password</h4>
				<button type="button" class="btn btn-sm fs-20" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<form id="changePSW">
					<div class="col-md-12">
						<div class="row">
							<div class="form-group basic">
								<label for="old_password">Old Password</label>
								<input type="password" name="old_password" id="old_password" class="form-control" placeholder="Old Password" />
								<div class="error old_password"></div>
							</div>

							<div class="form-group basic">
								<label for="new_password">New Password</label>
								<div class="input-group">
									<input type="password" name="new_password" id="new_password" class="form-control pswType" placeholder="Enter Password" value="">
									<div class="input-group-append">
										<button type="button" class="btn  btn-outline-primary pswHideShow"><i class="fa fa-eye"></i></button>
									</div>
								</div>
								<div class="error new_password"></div>
							</div>

							<div class="form-group  basic">
								<label for="cpassword">Confirm Password</label>
								<input type="text" name="cpassword" id="cpassword" class="form-control" placeholder="Confirm Password" />
								<div class="error cpassword"></div>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="fa fa-times"></i> Close</button>
				<button type="button" class="btn  btn-outline-success btn-save" onclick="changePsw('changePSW');"><i class="fa fa-check"></i> Save</button>
			</div>
		</div>
	</div>
</div>