<!DOCTYPE html>
<html lang="en">

<?php $this->load->view('includes/header'); ?>

<body>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <div class="navbar-header">
                    <!-- This is for the sidebar toggle which is visible on mobile only -->
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)">
                        <i class="ti-menu ti-close"></i>
                    </a>
                    <!-- ============================================================== -->
                    <!-- Logo -->
                    <!-- ============================================================== -->
                    <a class="navbar-brand" href="index.html" style="padding-top: 40px;">
                        <!-- Logo icon -->
                        <b class="logo-icon">
                            <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
                            <!-- Dark Logo icon -->
                            <img src="<?=base_url()?>assets/images/icon.png" alt="homepage" class="dark-logo" style="width:100%;" />
                            <!-- Light Logo icon -->
                            <img src="<?=base_url()?>assets/images/icon.png" alt="homepage" class="light-logo" style="width:100%;" />
                        </b>
                        <!--End Logo icon -->
                        <!-- Logo text -->
                        <span class="logo-text">
                            <!-- dark Logo text -->
                            <img src="<?=base_url()?>assets/images/logo_text.png" alt="homepage" class="dark-logo" style="width:100%;" />
                            <!-- Light Logo text -->
                            <img src="<?=base_url()?>assets/images/logo_text.png" alt="homepage" class="light-logo" style="width:100%;" />
                        </span>
                    </a>
                    <!-- ============================================================== -->
                    <!-- End Logo -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                    <!-- Toggle which is visible on mobile only -->
                    <!-- ============================================================== -->
                    <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)" data-toggle="collapse" data-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="ti-more"></i>
                    </a>
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse collapse" id="navbarSupportedContent">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav float-left mr-auto">
                        <li class="nav-item d-none d-md-block">
                            <a class="nav-link sidebartoggler waves-effect waves-light" href="javascript:void(0)" data-sidebartype="mini-sidebar">
                                <i class="mdi mdi-menu font-24"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="#config" aria-expanded="false">
                                <i class="far fa-circle"></i>
                                <span class="hide-menu">Configuration</span>
                            </a>
                        </li>
						<li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="#hr" aria-expanded="false">
                                <i class="far fa-circle"></i>
                                <span class="hide-menu">Human Resors</span>
                            </a>
                        </li>
						<li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="#purchase" aria-expanded="false">
                                <i class="far fa-circle"></i>
                                <span class="hide-menu">Purchase </span>
                            </a>
                        </li>
						<li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="#store" aria-expanded="false">
                                <i class="far fa-circle"></i>
                                <span class="hide-menu">Store </span>
                            </a>
                        </li>
						<li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="#maintenance" aria-expanded="false">
                                <i class="far fa-circle"></i>
                                <span class="hide-menu">Maintenance </span>
                            </a>
                        </li>
						<li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="#quality" aria-expanded="false">
                                <i class="far fa-circle"></i>
                                <span class="hide-menu">Quality </span>
                            </a>
                        </li>
						<li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="#dispatch" aria-expanded="false">
                                <i class="far fa-circle"></i>
                                <span class="hide-menu">Dispatch </span>
                            </a>
                        </li>
						<li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="#sales" aria-expanded="false">
                                <i class="far fa-circle"></i>
                                <span class="hide-menu">Sales </span>
                            </a>
                        </li>
						<li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="#account" aria-expanded="false">
                                <i class="far fa-circle"></i>
                                <span class="hide-menu">Account </span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                <div class="row p-t-30">
                    <div class="col-12">
                        <!-- card -->
                        <div class="card" id="config">
                            <div class="card-body">
                                <h3>Configuration</h3>
                                
                                <hr>
                                
                                <b><h4>Terms</h4></b>
                                <ul>
                                    <li><h6>How to add terms ?</h6></li>
                                    <li>In Configuration , There is a menu of "TERMS" in wich you can add the "Title" , "conditions" and "Type" of Your Terms. </li>
									<li>There are two buttons in action . All Details You'll add , Are Editable and Deletable. </li>
                                </ul>
                              
                                <b><h4>Shift</h4> </b>
                                <ul>
                                   <li><h6>How to add Shift ?</h6></li>
                                    <li>Here , You can add the Details of Shift , "Shift Name","Shift Start and End Time" and "Lunch Time",And save the details.
                                   <li>There is one button in action . All Details You'll add Are Editable . </li>
                                </ul>
								<b><h4>Master Option</h4> </b>
                                <ul>
                                    <li>There are Direct Input Boxes ,You can add the Details 
                                   <li>In Top Right Corner , There is one button of Save .</li>
								   <li>Using this button you can save the details and these details are removable and addable.</li>
                                </ul>
								<b><h4>Currency </h4> </b>
                                <ul>
                                   <li>Here Is the list of currency Name, Code, Symbol and INR Rate. </li> 
                                   <li>In Top Right Corner , There is one button of Save .</li>
								   <li>INR Rate is editable.</li>
                                </ul>
								<b><h4>Material Grade </h4> </b>
								<ul>
									<li>In this menu you can add Material Grade , Standard , Scrap Group and Colour Code.  
									<li>There are two buttons in action . All Details You'll add , Are Editable and Deletable. </li>
                                </ul>
								<b><h4>Tax Master </h4> </b>
								<ul>
									<li>Here you can add Taxes . </li>
									<li>With the help of ADD Button in top right corner. </li>
									<li>There are two buttons in action . All Details You'll add , Are Editable and Deletable. </li>
                                </ul>
								<b><h4>Expense Master </h4> </b>
								<ul>
									<li>This is a master form of Expenses.</li>
									<li>Expense name, Type, Ledger name, Calcu.Type, Def.Per ETC. </li>
									<li>There is button in action . All Details You'll add Are Editable. </li>
                                </ul>
								<b><h4>Contact Directory </h4> </b>
								<ul>
									<li>Its also define as Holiday note.</li>
									<li>Here you can add the details from "Add Button" </li>
									<li>There are two buttons in action . All Details You'll add , Are Editable and Deletable. </li>
                                </ul>
									<b><h4>HSN Master </h4> </b>
								<ul>
									<li>In HSN Master , You can add HSN Code, GST Percentages and Discription.</li>
									<li>There are two buttons in action . All Details You'll add , Are Editable and Deletable. </li>
                                </ul>
									<b><h4>Category</h4> </b>
								<ul>
									<li>In Category Master , You can add Category Name and Over Time.</li>
									<li>There are two buttons in action . All Details You'll add , Are Editable and Deletable. </li>
                                </ul>
								<b><h4>Grade Master</h4> </b>
								<ul>
									<li>This is the master of grades.</li>
									<li>In form you can see many input boxes. "Chemical Composition", "Mechanical Properties","Hardness" given below.</li>
									<li>There are two buttons in action . All Details You'll add , Are Editable and Deletable. </li>
                                </ul>
                            </div>
                        </div>
                        <!-- card -->
						<div class="card" id="hr">
                            <div class="card-body">
                                <h3>Human Resource</h3>
                                
                                <hr>
                                <b><h4>Department</h4></b>
                                <ul>
                                    <li>In Human Resource , There is a menu of "Department" in wich you can add the "Department Name" and "category". </li>
									<li>There is one buttons in action . All Details You'll add Are Editable. </li>
                                </ul>
								<b><h4>Designation</h4></b>
                                <ul>
                                    <li>Here , You can add Designation Name and remark.</li> 
									<li>There are two buttons in action . All Details You'll add , Are Editable and Deletable. </li>
                                </ul>
								<b><h4>Employee</h4></b>
                                <ul>
                                    <li>Here , You can add The Details Of Employees.</li> 
									<li>There are many buttons in action . </li>
									<li>Employee Salary , Documents , Nomination , Education , Leave And Relieve Data.</li>
									<li>All The Details You'll Add Are Editable.</li>
                                </ul>
								<b><h4>Relieved Employee</h4></b>
                                <ul>
                                    <li>Here , You can See the list of relieved employees.</li> 
                                </ul>
								<b><h4>Attendance</h4></b>
                                <ul>
                                    <li>In Top Right corner there is filter of date, by using this you can get the data of Employee Attendance Data.</li> 
                                </ul>
									<b><h4>Leave Setting</h4></b>
                                <ul>
                                    <li>Here , You can manage the data of employee's leave.</li> 
									<li>There are two buttons in action . All Details You'll add , Are Editable and Deletable. </li>
                                </ul>
								<b><h4>Leave Request</h4></b>
                                <ul>
                                    <li>In Leave Request , You must add the details of your leave.</li> 
									<li>There are two buttons in action . All Details You'll add , Are Editable and Deletable. </li>
                                </ul>
								<b><h4>Leave Approve</h4></b>
                                <ul>
                                    <li>Here, You can check that your leave has been approved or not.</li> 
                                </ul>
								<b><h4>Manual Attendance</h4></b>
                                <ul>
                                    <li>In this you can add employee name , attendance date and punch time.</li> 
									<li>There are two buttons in action . All Details You'll add , Are Editable and Deletable. </li>
                                </ul>
								<b><h4>Extra Hours</h4></b>
                                <ul>
                                    <li>Here, You can add the extra hours's details.</li> 
									<li>There are two buttons in action . All Details You'll add , Are Editable and Deletable. </li>
                                </ul>
								<b><h4>Advance salary</h4></b>
                                <ul>
                                    <li>There is Record of advance salary, you can add the details of amount and employes</li> 
									<li>There are two buttons in action . All Details You'll add , Are Editable and Deletable. </li>
                                </ul>
								<b><h4>Employee Loan</h4></b>
                                <ul>
                                    <li>Here, you can add the details of loan.</li> 
									<li>There are two buttons in action . All Details You'll add , Are Editable and Deletable. </li>
                                </ul>
                            </div>
                        </div>
						<!-- card -->
						<div class="card" id="purchase">
                            <div class="card-body">
                                <h3>Purchase</h3>
                                
                                <hr>
                                <b><h4>Item Category</h4></b>
                                <ul>
                                    <li>Here, You Can Add Items And Its Category Type. </li>
									<li>Each Information You’ll Add , Will Be Editable And Deletable. </li>
                                </ul>
								<b><h4>Supplier</h4></b>
                                <ul>
                                    <li>This Is Generally Used As a Master Data In Other Module.</li> 
									<li>You Can Add/Edit/Delete Vendor Detail.</li> 
                                </ul>
								<b><h4>Raw Material</h4></b>
                                <ul>
                                    <li>Here , You can add The Details Of materials.</li> 
									<li>All The Details You'll Add Are Editable and Deletable.</li>
                                </ul>
								<b><h4>Capital Goods</h4></b>
                                <ul>
                                    <li>In this you can the details of capital goods.</li> 
									<li>All The Details You'll Add Are Editable and Deletable.</li>
                                </ul>
								<b><h4>Purchase Indent</h4></b>
                                <ul>
                                    <li>At first you have to create purchase indent, After this you can create enquiry and order. </li> 
                                </ul>
								<b><h4>Purchase Enquiry</h4></b>
                                <ul>
                                    <li>Here, you can make enquiry of an order</li> 
                                </ul>
								<b><h4>Purchase Order</h4></b>
                                <ul>
                                    <li>You Can Add/Edit/Delete Purchase Order And Also You Can Receive Order By The Regarding Buttons.</li> 
                                </ul>
                            </div>
                        </div>
						<!-- card -->
						<div class="card" id="store">
                            <div class="card-body">
                                <h3>Store</h3>
                                
                                <hr>
                                <b><h4>Store Location </h4></b>
                                <ul>
                                    <li>Here, You Can Add Location and store name. </li>
									<li>Each Information You’ll Add , Will Be Editable And Deletable. </li>
                                </ul>
								<b><h4>Material Issue</h4></b>
                                <ul>
                                    <li>Here, You can issue the material against job card</li> 
                                </ul>
								<b><h4>Tool Issue</h4></b>
                                <ul>
                                    <li>In this you can add the details of tools.</li> 
									<li>All The Details You'll Add Are Editable and Deletable.</li>
                                </ul>
								<b><h4>General Issue</h4></b>
                                <ul>
                                    <li>here, you can the details of General issue.</li> 
									<li>All The Details You'll Add Are Editable and Deletable.</li>
                                </ul>
								<b><h4>Consumable</h4></b>
                                <ul>
                                    <li>In this you can add the Item Details , Wich Is known as Consumable Items.</li> 
									<li>All The Details You'll Add Are Editable and Deletable.</li>
                                </ul>
								<b><h4>Stock Ledger</h4></b>
                                <ul>
                                    <li>In this menu, you can see the stock details.</li> 
                                </ul>
								<b><h4>Stock Verification</h4></b>
                                <ul>
                                    <li>Here , you can verify stocks.</li> 
                                </ul>
								<b><h4>GRN</h4></b>
                                <ul>
                                    <li>In this , You can add the details of GRN(Goods Receipt Note)</li> 
									<i>There is a print button in action, With the use of it , You Can get the print of grn.</li> 
                                </ul>
                            </div>
                        </div>
						<!-- card -->
						<div class="card" id="maintenance">
                            <div class="card-body">
                                <h3>Maintenance</h3>
                                
                                <hr>
                                <b><h4>Machine</h4></b>
                                <ul>
                                    <li>Here, You Can Add The details of items(Machine). </li>
									<li>Each Information You’ll Add , Will Be Editable And Deletable. </li>
                                </ul>
								<b><h4>Machine Ticket</h4></b>
                                <ul>
                                    <li>In this module , You can store machine ticket data.</li> 
									<li>Each Information You’ll Add , Will Be Editable And Deletable. </li>
                                </ul>
								<b><h4>Machine Activities</h4></b>
                                <ul>
                                    <li>Here you can add the activity of machine.</li> 
									<li>All The Details You'll Add Are Editable.</li>
                                </ul>
                            </div>
                        </div>
						<!-- card -->
						<div class="card" id="quality">
                            <div class="card-body">
                                <h3>Quality</h3>
                                
                                <hr>
                                <b><h4>Inward QC</h4></b>
                                <ul>
                                    <li>After Puchase process in wich you can check the quality is called Inward QC.</li>
									<li>There are two buttons is action,Inspection and TC parameter.</li>
                                </ul>
								<b><h4>Rejection Reason </h4></b>
                                <ul>
                                    <li>In this module , You can add the reason of rejection.</li> 
									<li>Each Information You’ll Add , Will Be Editable And Deletable. </li>
                                </ul>
								<b><h4>Gauges</h4></b>
                                <ul>
                                    <li>Here you can add Item Name and its Details.</li> 
									<li>All The Details You'll Add Are Editable.</li>
                                </ul>
								<b><h4>Instrument</h4></b>
                                <ul>
                                    <li>Here you can add Item Name and its Details.</li> 
									<li>All The Details You'll Add Are Editable.</li>
                                </ul>
								<b><h4>In Challan</h4></b>
                                <ul>
                                    <li>In this you can add the details of In Challan.</li> 
									<li>All The Details You'll Add Are Editable.</li>
                                </ul>
								<b><h4>Out Challan</h4></b>
                                <ul>
                                    <li>In this you can add the details of Out Challan.</li> 
									<li>All The Details You'll Add Are Editable.</li>
                                </ul>
								<b><h4>Pre Dispatch Inspection</h4></b>
                                <ul>
                                    <li>Here, you can add pre dispatch inspection.</li> 
									<li>All The Details You'll Add Are Editable.</li>
                                </ul>
								<b><h4>RM Inspection Param</h4></b>
                                <ul>
                                    <li>Here, you can see inspection button in Action.</li> 
									<li>By wich you can the inspection details</li>
                                </ul>
								<b><h4>Inspection Type</h4></b>
                                <ul>
                                    <li>In this you can add Inspection Type.</li> 
									<li>wich are editable and deletable.</li>
                                </ul>
								<b><h4>Control Plan</h4></b>
                                <ul>
                                    <li>Here, You can see product inspection parameter.</li> 
                                </ul>
								<b><h4>Line Inspection </h4></b>
                                <ul>
                                    <li>Here, You can add the Line Inspection.</li> 
									<li>wich are editable and deletable.</li>
                                </ul>
                            </div>
                        </div>
						<!-- card -->
						<div class="card" id="dispatch">
                            <div class="card-body">
                                <h3>Dispatch</h3>
                                
                                <hr>
                                <b><h4>Packing</h4></b>
                                <ul>
                                    <li>In this you can add packing details.</li>
									<li>There are 5 filter tab in front left corner.</li>
									
                                </ul>
								<b><h4>Delivery Challan</h4></b>
                                <ul>
                                    <li>Here,you can make challan of sales items.</li> 
									<li>Each Information You’ll Add , Will Be Editable And Deletable. </li>
                                </ul>
								<b><h4>Stock Ledger</h4></b>
                                <ul>
                                    <li>In this you can see the stock data by item type.</li> 
									
                                </ul>
								<b><h4>Stock Adjustment</h4></b>
                                <ul>
                                    <li>Here, You can manage the stock by having the details of stock.</li> 
                                </ul>
                            </div>
                        </div>
						<!-- card -->
						<div class="card" id="sales">
                            <div class="card-body">
                                <h3>Sales</h3>
                                
                                <hr>
                                <b><h4>Customer</h4></b>
                                <ul>
                                    <li>In this you can add Customer details.</li>
									<li>All the details are editable and deletable.</li>
									
                                </ul>
								<b><h4>Non Feasibility Reason </h4></b>
                                <ul>
                                    <li>Here,you can add the details of rejection type and reason.</li> 
									<li>Each Information You’ll Add , Will Be Editable And Deletable. </li>
                                </ul>
								<b><h4>Sales Enquiry</h4></b>
                                <ul>
                                    <li>This Is Generally Used As A Master Data In Other Modules</li> 
									<li>You Can Add/Edit/Delete Sales Enquiry Details.</li>
                                </ul>
								<b><h4>Sales Quotation</h4></b>
                                <ul>
                                    <li>This Is Generally Used As A Master Deta In Other Modules.</li> 
									<li>You Can Print/Add/Edit/Delete Sales Quotaion’s Details.</li>
                                </ul>
								<b><h4>Proforma Invoice </h4></b>
                                <ul>
                                    <li>In this you can make a invoice.</li> 
									<li>All the details are changable.</li> 
                                </ul>
								<b><h4>Sales Order</h4></b>
                                <ul>
                                    <li>You Can Show Complete Pendding Sales Orders, And Update Dispatch Schedule By Regarding Buttons, And print Button Is Also Available.</li> 
									<li>All the details are changable.</li> 
                                </ul>
								<b><h4>Packing Request </h4></b>
                                <ul>
									<li>In this You can send the request of packing.</li> 
									<li>All the details are changable.</li> 
                                </ul>
								<b><h4>Sales Target </h4></b>
                                <ul>
									<li>In this You can filter the data of sales target.</li> 
                                </ul>
								<b><h4>Finish Goods </h4></b>
                                <ul>
									<li>Here, you can add the details of products(Finish Goods).</li> 
									<li>All the details are editable and deletable.</li> 
                                </ul>
                            </div>
                        </div>
						<!-- card -->
						<div class="card" id="account">
                            <div class="card-body">
                                <h3>Account</h3>
                                
                                <hr>
                                <b><h4>Ledger</h4></b>
                                <ul>
                                    <li>In this you can add Ledger details.</li>
									<li>All the details are editable and deletable.</li>
									
                                </ul>
								<b><h4>Service Item</h4></b>
                                <ul>
                                    <li>Here,you can add the details of Service item.</li> 
									<li>Each Information You’ll Add , Will Be Editable And Deletable. </li>
                                </ul>
								<b><h4>purchase Invoice</h4></b>
                                <ul>
                                    <li>In this you can add invoice of purchase items.</li> 
									<li>You Can Add/Edit/Delete Purchase Invoice Details.</li>
                                </ul>
								<b><h4>Debit Note</h4></b>
                                <ul>
                                    <li>Here, you can add debit note details.</li> 
									<li>You Can Add/Edit/Delete the Details.</li>
                                </ul>
								<b><h4>Payments</h4></b>
                                <ul>
                                    <li>In this you can add payment voucher details.</li> 
									<li>All the details are changable.</li> 
                                </ul>
								<b><h4>Commercial Packing</h4></b>
                                <ul>
									<li>In this you can add commercial packing details.</li> 
                                </ul>
								<b><h4>Commercial Invoice </h4></b>
                                <ul>
									<li>In this You can add commercial invoice data.</li> 
                                </ul>
								<b><h4>Custom packing </h4></b>
                                <ul>
									<li>In this You can add custom packing data.</li> 
                                </ul>
								<b><h4>Custom Invoice </h4></b>
                                <ul>
									<li>Here, you can add the details of Custom Invoice.</li> 
									<li>All the details are editable and deletable.</li> 
                                </ul>
								<b><h4>Tax Invoice </h4></b>
                                <ul>
									<li>In this you can make an Invoice By Sales order. </li> 
									<li>All the details are editable and deletable</li>
                                </ul>
								<b><h4>Credit Note</h4></b>
                                <ul>
									<li>In this you can add the data of credit note.</li> 
									<li>All the details are editable and deletable.</li> 
                                </ul>
								<b><h4>GST Expense</h4></b>
                                <ul>
									<li>In this you can add the details of gst expense.</li> 
									<li>All the details are editable and deletable.</li> 
                                </ul>
								<b><h4>Journal Entry </h4></b>
                                <ul>
									<li>Here, you can add the entry of journal.</li> > 
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- End Page Content -->
                <!-- ============================================================== -->
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
           
            <!-- ============================================================== -->
            <!-- End footer -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="../assets/libs/popper.js/dist/umd/popper.min.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- apps -->
    <script src="../dist/js/app.js"></script>
    <script src="../dist/js/app.init.js"></script>
    <script src="../dist/js/app-style-switcher.js"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="../assets/libs/perfect-scrollbar/dist/js/perfect-scrollbar.jquery.min.js"></script>
    <script src="../assets/extra-libs/sparkline/sparkline.js"></script>
    <!--Menu sidebar -->
    <script src="../dist/js/sidebarmenu.js"></script>
    <!--Custom JavaScript -->
    <script src="../assets/extra-libs/prism/prism.js"></script>
    <script src="../dist/js/custom.min.js"></script>
    <script>
        $('#sidebarnav a').click(function () {
            $('html, body').animate({
                scrollTop: $($(this).attr('href')).offset().top - 85
            }, 500);
            return false;
        });
        var lastId, topMenu = $("#sidebarnav"),
            topMenuHeight = topMenu.outerHeight(),
            menuItems = topMenu.find("a"),
            scrollItems = menuItems.map(function () {
                var item = $($(this).attr("href"));
                if (item.length) {
                    return item;
                }
            });
        $(window).scroll(function () {
            var fromTop = $(this).scrollTop() + topMenuHeight - 85;
            var cur = scrollItems.map(function () {
                if ($(this).offset().top < fromTop) return this;
            });
            cur = cur[cur.length - 1];
            var id = cur && cur.length ? cur[0].id : "";
            if (lastId !== id) {
                lastId = id;
                menuItems.removeClass("active").filter("[href='#" + id + "']").addClass("active");
            }
        });
    </script>
</body>
<?php $this->load->view('includes/footer'); ?>