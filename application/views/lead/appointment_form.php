<form>
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="lead_id" id="lead_id" value="">

            <div class="col-md-6 form-group">
                <label for="appointment_date">Appintment Date</label>
                <input type="date" name="appointment_date" id="appointment_date" class="form-control req" value="<?=(!empty($dataRow->appointment_date))?$dataRow->appointment_date:date("Y-m-d")?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="appointment_time">Appintment Time</label>
                <input type="time" name="appointment_time" id="appointment_time" class="form-control req" value="<?=(!empty($dataRow->appointment_time))?date("h:i:s",strtotime($dataRow->appointment_time)):date("h:i:s")?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="mode">Mode</label>
                <select name="mode" id="mode" class="form-control req single-select">
                    <?php
                        foreach($appointmentMode as $row):
							$selected = (!empty($dataRow->mode) and $dataRow->mode == $row)?"selected":"";
                            echo '<option value="'.$row.'" '.$selected .'>'.$row.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
			<div class="col-md-6 form-group">
                <label for="contact_person">Contact Person</label>
                <input type="text" name="contact_person" id="contact_person" class="form-control text-capitalize req" value="<?=(!empty($dataRow->contact_person))?$dataRow->contact_person:""?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="notes">Notes</label>
                <textarea name="notes" id="notes" class="form-control"><?=(!empty($dataRow->notes))?$dataRow->notes:""?></textarea>
            </div>
        </div>
    </div>    
</form>
<hr>
<style>#appointmentTable td,#appointmentTable th{font-size:0.8rem;}</style>
<div class="col-md-12">
    <div class="row">
        <label for="">Appointments : </label>
        <div class="table-responsive">
            <table id='appointmentTable' class="table table-bordered">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Appointment Schedule</th>
                        <th>Mode</th>
                        <th>Appointment With</th>
                        <th>Notes</th>
                        <th style="width:10%;">Action</th>
                    </tr>                            
                </thead>
                <tbody id="appointmentData">
					<?php
						if(!empty($appintmentData))
						{
							$i=1;
							foreach($appintmentData as $row)
							{
								$deleteParam = $row->id.",'deleteAppointment','Appointment'";
								echo '<tr>';
									echo '<td clas="text-center">'.$i++.'</td>';
									echo '<td clas="text-center">'.formatDate($row->appointment_date,'d-m-Y ').formatDate($row->appointment_time,'H:i A').'</td>';
									echo '<td>'.$row->mode.'</td>';
									echo '<td>'.$row->contact_person.'</td>';
									echo '<td>'.$row->notes.'</td>';
									echo '<td clas="text-center"><button type="button" onclick="trashLead('.$deleteParam.');"  class="btn btn-outline-danger waves-effect waves-light" style="padding:2px 8px;"><i class="ti-trash"></i></button></td>';
								echo '</tr>';
							}
						}
					?>
                </tbody>
            </table>
        </div>
    </div>
</div>
    
