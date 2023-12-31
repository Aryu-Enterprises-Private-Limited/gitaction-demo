<!-- Start content -->
<?= $this->extend('layout') ?>
<style>
    img,
    figure {
        max-width: 100%;
        max-height: 100%;
        margin: 0;
        padding: 0;
    }
</style>
<?= $this->section('content') ?>
<div class="container-fluid mt-4">
    <div class="card create-box">
        <div class="card-body">
            <div class="row">
                <div class="col-md-11">
                    <ol class="breadcrumb p-0 m-0">
                        <li class="breadcrumb-item bread-home"><a href="<?= '/' . ADMIN_PATH . '/dashboard' ?>"><i class="fa fa-home me-0" aria-hidden="true"></i></a></li>
                        <li class="breadcrumb-item">
                            <a class="text-decoration-none" href="<?= '/' . ADMIN_PATH . '/pay/list' ?>"><?php echo  'Pay'; ?> </a>
                        </li>
                        <?php if (isset($pay_info) && $pay_info->employee_name) { ?>
                            <li class="breadcrumb-item">
                                <?php echo $pay_info->employee_name; ?>
                            </li>
                            <li class="breadcrumb-item active">
                                <?php echo 'Edit'; ?>
                            </li>
                        <?php } else { ?>
                            <li class="breadcrumb-item active">
                                <?php echo 'Add New'; ?>
                            </li>
                        <?php } ?>
                    </ol>
                    <hr>
                    <h3><?= $title;  ?></h3>
                </div>
            </div>

            <div class="create-label">
                <form id="pay_form" method="post" action="<?= (base_url(ADMIN_PATH . '/pay/update'))  ?>" autocomplete="off" enctype="multipart/form-data">
                    <input type="hidden" id="id" name="id" value="<?php if (isset($pay_info) && $pay_info->id) echo $pay_info->id; ?>">
                    <div class="mb-3 row ">
                        <label class="col-sm-2 col-form-label fw-bold">Employee Name <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select name="employee_name" class="form-control create-input required" id="employee_name">
                                <option value="">Select</option>
                                <?php foreach ($employee_details as $key => $value) {
                                    $selected = '';
                                    if (isset($pay_info->employee_id) && $pay_info->employee_id == $value->id) {
                                        $selected = 'selected';
                                    }
                                ?>
                                    <option value="<?php echo $value->id . ',' . ucfirst($value->first_name) . ' ' . ucfirst($value->last_name); ?>" <?= $selected; ?>>
                                        <?php echo ucfirst($value->first_name); ?> <?php echo ucfirst($value->last_name); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label fw-bold">Monthly Salary <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control create-input numberonly" name="month_sal" id="month_sal" value="<?php if (isset($pay_info->month_sal)) echo $pay_info->month_sal; ?>" required>
                        </div>
                    </div>

                    <div id="addFieldDiv">

                        <?php
                        if (isset($pay_info) && !empty($pay_info->revisied_dt) && !empty($pay_info->comment)) {
                            $revisied_dt = json_decode($pay_info->revisied_dt);
                            $comment = json_decode($pay_info->comment);
                            //foreach ($revisied_dt as $data) {
                            for ($i = 0; $i < count($revisied_dt); $i++) {
                                // echo $i;

                        ?>
                                <div class="fieldDiv">
                                    <div class="mb-3 row field_wrapper"><label class="col-sm-2 col-form-label fw-bold ">Revised Date </label>
                                        <div class="col-sm-10"><input placeholder="revised date" type="date" class="form-control create-input " name="revisied_dt[]" value="<?php if (isset($revisied_dt)) echo $revisied_dt[$i]; ?>" required></div><a href="javascript:void(0);" class="remove_button"><img /></a>
                                    </div>
                                    <?php  //}
                                    //foreach($comment as $data){

                                    ?>
                                    <div class="mb-3 row field_wrapper2"><label class="col-sm-2 col-form-label fw-bold"> Comments </label>
                                        <div class="col-sm-10">
                                            <textarea placeholder="comments" class="form-control create-input" rows="3" name="comment[]" required><?php if (isset($comment)) echo $comment[$i]; ?></textarea>
                                            <div class="text-end create-input"><a href="javascript:void(0);" class="remove_button"><img src="/images/remove-icon.png" /></a></div>
                                        </div>
                                    </div>
                                </div>
                        <?php  }
                        }
                        ?>

                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label fw-bold"> </label>
                        <div class="col-sm-6 ">
                            <div class="create-input text-end"><a href="javascript:void(0);" class="add_button " title="Add field"><img src="/images/add-icon.png" /></a></div>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <?php if (isset($pay_info) && isset($pay_info->status) && $pay_info->status == '1') $sT = 'checked="checked"';
                        else $sT = ''; ?>
                        <label class="col-sm-2 col-form-label fw-bold">Status</label>
                        <div class="form-check form-switch col-sm-10">
                            <input class="form-check-input form-control" type="checkbox" name="status" <?php echo $sT; ?>>
                        </div>
                    </div>
                    <button type="button" class="btn butn-submit text-white sbmtBtn" id="btn">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(".sbmtBtn").click(function(evt) {
            if ($('#pay_form').valid()) {
                $('.sbmtBtn').attr("disabled", true);
                $('#pay_form').submit();
            }
        });

        var maxField = 10; //Input fields increment limitation
        var addButton = $('.add_button'); //Add button selector
        var wrapper = $('.field_wrapper'); //Input field wrapper
        var wrapper2 = $('.field_wrapper2');
        // var fieldHTML = '<div><input type="text" name="field_name[]" value=""/><a href="javascript:void(0);" class="remove_button"><img src="/images/remove-icon.png"/></a></div>'; //New input field html 

        var fieldHTML2 = '<div class="mb-3 row field_wrapper2"><label class="col-sm-2 col-form-label fw-bold">Comments </label><div class="col-sm-10"><textarea placeholder="comments"class="form-control create-input" rows="3" name="comment[]" required></textarea><div class="text-end create-input"><a href="javascript:void(0);" class="remove_button"><img src="/images/remove-icon.png"/></a></div></div></div>';

        var fieldHTML = '<div class="mb-3 row field_wrapper"><label class="col-sm-2 col-form-label fw-bold ">Revised Date </label><div class="col-sm-10"><input placeholder="revised date" type="date" class="form-control create-input " name="revisied_dt[]"  required></div><a href="javascript:void(0);" class="remove_button"><img /></a></div>';

        var x = 1; //Initial field counter is 1

        // Once add button is clicked
        $(addButton).click(function() {
            //Check maximum number of input fields
            if (x < maxField) {
                x++; //Increase field counter
                // $(wrapper).append(fieldHTML2); //Add field html
                // $(wrapper2).append(fieldHTML);
                $('#addFieldDiv').append('<div class="fieldDiv">' + fieldHTML + fieldHTML2 + '</div>');
                // $('#addFieldDiv').append(fieldHTML2);
            } else {
                alert('A maximum of ' + maxField + ' fields are allowed to be added. ');
            }
        });

        // Once remove button is clicked
        $('body').on('click', '.remove_button', function(e) {
            e.preventDefault();
            $(this).parent().parent().parent().parent('div.fieldDiv').remove(); //Remove field html
            x--; //Decrease field counter
        });

        $('.numberonly').keypress(function(e) {
            var charCode = (e.which) ? e.which : event.keyCode
            if (String.fromCharCode(charCode).match(/[^0-9.]/g))
                return false;
        });
    });
</script>
<?= $this->endSection() ?>