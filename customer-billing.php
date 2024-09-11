<?php include 'includes/header.php'; ?>

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Add New Customer</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="form">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label class="control-label">Date</label>
                                            <input type="date" name="date" id="todaydate" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="name" class="control-label">Customer Name</label>
                                            <input type="text" name="name" id="customerName" required class="form-control" placeholder="Enter Customer Name" />
                                        </div>
                                        <div class="col-md-12 mb-3 form-group">
                                            <label for="address" class="control-label">Address: *</label>
                                            <textarea name="customerAddress" class="form-control" id="address" rows="3" required="required"></textarea>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="city" class="control-label">City: *</label>
                                            <input type="text" id="city" class="form-control" required name="customerCity" placeholder="City">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="mobile" class="control-label">Mobile Number</label>
                                            <input type="number" class="form-control" id="mobile" required name="customerMobile" placeholder="Customer Mobile No.">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="dob" class="control-label">Date Of Birth</label>
                                            <input type="date" class="form-control" required id="dob" name="customerDOB">
                                        </div>
                                    </div>
                                    <button class="btn btn-primary nextBtn btn-lg pull-right" type="button">Add</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php';
