
			<footer class="footer text-center"></footer>
		</div>
	</div>
		<?php $this->load->view('app/includes/modal_md');?>
		<?php $this->load->view('app/includes/modal_lg');?>
		<?php $this->load->view('app/includes/modal_xl');?>
		<?php $this->load->view('app/includes/modal_xxl');?>
		<?php //$this->load->view('includes/modal_master');?>
		<?php //$this->load->view('app/includes/custom_panel');?>
		<?php $this->load->view('app/includes/footerfiles');?>
							<!-- Form Action Sheet -->
<div class="modal fade action-sheet" id="actionSheetForm" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Add New Bill</h5>
			</div>
			<div class="modal-body">
				<div class="action-sheet-content">

					<form>
						<div class="form-group basic">
							<label class="label">Bill ID</label>
							<div class="input-group">
								<span class="input-group-text" id="basic-addon1">#</span>
								<input type="text" class="form-control" placeholder="Enter an amount" value="41512">
							</div>
							<div class="input-info">Enter the ID of the bill you want to add.</div>
						</div>


						<div class="form-group basic">
							<button type="button" class="btn btn-primary btn-block btn-lg"
								data-bs-dismiss="modal">Add</button>
						</div>
					</form>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn waves-effect waves-light btn-outline-danger btn-close save-form" data-bs-dismiss="modal"><i class="fa fa-times"></i>Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form"><i class="fa fa-check"></i>Save</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade action-sheet" id="actionSheetForm2" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Add New Bill</h5>
			</div>
			<div class="modal-body">
				<div class="action-sheet-content">

					<form>
						<div class="form-group basic">
							<label class="label">Bill ID</label>
							<div class="input-group">
								<span class="input-group-text" id="basic-addon1">#</span>
								<input type="text" class="form-control" placeholder="Enter an amount" value="41512">
							</div>
							<div class="input-info">Enter the ID of the bill you want to add.</div>
						</div>


						<div class="form-group basic">
							<button type="button" class="btn btn-primary btn-block btn-lg"
								data-bs-dismiss="modal">Add</button>
						</div>
					</form>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn waves-effect waves-light btn-outline-danger btn-close save-form" data-bs-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form"><i class="fa fa-check"></i> Save</button>
			</div>
		</div>
	</div>
</div>
	</body>
</html>