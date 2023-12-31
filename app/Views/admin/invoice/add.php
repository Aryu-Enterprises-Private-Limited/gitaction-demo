<!-- Start content -->
<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<?php if (isset($form_data['id'])) {
    $req = ''
?>

<?php } else {
    $req = 'required';
}  ?>

<div class="container-fluid mt-4">
    <div class="card create-box">
        <div class="card-body">
            <div class="row">
                <div class="col-md-11">
                    <h3><?= $title;  ?></h3>
                </div>
            </div>

            <div class="create-label">
                <form id="invoice_form" method="post" action="<?= (base_url(ADMIN_PATH . '/invoice/gen_invoice'))  ?>" autocomplete="off" enctype="multipart/form-data">
                    <input type="hidden" id="id" name="id" value="<?php if (isset($form_data) && $form_data['id']) echo $form_data['id']; ?>">
                    <!-- <div class="mb-3 row ">
                        <label class="col-sm-2 col-form-label fw-bold">From <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select name="from_name" class="form-control create-input" id="from_name" required>
                                <option value=""> select</option>
                                <?php foreach ($client_opt as $key => $value) {
                                    $selected = '';
                                    if (isset($form_data['from_name']) && $form_data['from_name'] == ucfirst($value->first_name) . ' ' . ucfirst($value->last_name)) {
                                        $selected = 'selected';
                                    }
                                ?>
                                    <option value="<?php echo ($value->first_name) . ' ' . ($value->last_name) . ',' . $value->id; ?>" <?= $selected; ?>>
                                        <?php echo ucfirst($value->first_name); ?> <?php echo ucfirst($value->last_name); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div> -->
                    <div class="mb-3 row ">
                        <label class="col-sm-2 col-form-label fw-bold">From <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select name="from_name" class="form-control create-input" id="from_name" required>
                                <option value=""> Select</option>
                                <option value="ARYU ENTERPRISES PRIVATE LIMITED" selected>
                                    ARYU ENTERPRISES PRIVATE LIMITED
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row ">
                        <label class="col-sm-2 col-form-label fw-bold"> To <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select name="to_name" class="form-control create-input" id="to_name" required>
                                <option value=""> Select</option>
                                <?php foreach ($client_opt as $key => $value) {
                                    $selected = '';
                                    $to_name = '';
                                    if (isset($form_data['to_name'])) {
                                        $to_name = $form_data['to_name'];
                                    }
                                    $to_arr = explode(",", $to_name);
                                    if (isset($form_data['to_name']) && $to_arr[0] == ($value->first_name) . ' ' . ($value->last_name)) {
                                        $selected = 'selected';
                                    }
                                ?>
                                    <option value="<?php echo ($value->first_name) . ' ' . ($value->last_name) . ',' . $value->id; ?>" <?= $selected; ?>>
                                        <?php echo ucfirst($value->first_name); ?> <?php echo ucfirst($value->last_name); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label fw-bold">Description <span class="text-danger">*</span></label>
                        <div class="col-sm-10 input-container">
                            <div class="input-group input-groups control-group after-add-more col-sm-10">
                                <input type="text" name="addmore[]" class="form-control" id="addmore" placeholder="Description" value="" <?= $req; ?>>
                                <div class="input-group-btn">
                                    <button class="btn btn-success add-more" type="button"><i class="glyphicon glyphicon-plus"></i> Add</button>
                                </div>
                            </div>
                            <?php
                            if (isset($form_data['addmore'])) {
                                foreach ($form_data['addmore'] as $data) {
                                    if ($data != '') {
                            ?>
                                        <div class="mb-3 row copy hide_show">
                                            <div class="col-sm-10 ">
                                                <div class="control-group input-group input-grp removemore" style="margin-top:10px">
                                                    <input type="text" name="addmore[]" class="form-control" value="<?php if (isset($data)) echo $data; ?>">
                                                    <div class="input-group-btn">
                                                        <button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                            <?php }
                            } ?>
                        </div>

                    </div>

                    <div class="mb-3 row copy hide">
                        <div class="col-sm-10 ">
                            <div class="control-group input-group input-grp removemore" style="margin-top:10px">
                                <input type="text" class="form-control">
                                <div class="input-group-btn">
                                    <button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label fw-bold"> Amount <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <div class="input-group input-groups control-group after-add-more1 col-sm-10">
                                <input type="text" name="amntmore[]" class="form-control" placeholder="Amount" <?= $req; ?>>
                                <div class="input-group-btn">
                                    <button class="btn btn-success add-more1" type="button"><i class="glyphicon glyphicon-plus"></i> Add</button>
                                </div>
                            </div>
                            <?php
                            if (isset($form_data['amntmore'])) {
                                foreach ($form_data['amntmore'] as $data) {
                                    if ($data != '') {
                            ?>
                                        <div class="mb-3 row copy  hide_show">
                                            <div class="col-sm-10 ">
                                                <div class="control-group input-group input-grp removemore" style="margin-top:10px">
                                                    <input type="text" name="amntmore[]" class="form-control" value="<?php if (isset($data)) echo $data; ?>">
                                                    <div class="input-group-btn">
                                                        <button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                            <?php }
                                }
                            } ?>
                        </div>
                    </div>

                    <div class="mb-3 row copy1 hide">
                        <div class="col-sm-10 ">
                            <div class="control-group input-group input-grp removemore" style="margin-top:10px">
                                <input type="text" class="form-control">
                                <div class="input-group-btn">
                                    <button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label fw-bold"> Quantity <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <div class="input-group input-groups control-group after-add-more2 col-sm-10">
                                <input type="text" name="qntymore[]" class="form-control" placeholder="Quantity" <?= $req; ?>>
                                <div class="input-group-btn">
                                    <button class="btn btn-success add-more2" type="button"><i class="glyphicon glyphicon-plus"></i> Add</button>
                                </div>
                            </div>
                            <?php
                            if (isset($form_data['qntymore'])) {
                                foreach ($form_data['qntymore'] as $data) {
                                    if ($data != '') {
                            ?>
                                        <div class="mb-3 row copy  hide_show">
                                            <div class="col-sm-10 ">
                                                <div class="control-group input-group input-grp removemore" style="margin-top:10px">
                                                    <input type="text" name="qntymore[]" class="form-control" value="<?php if (isset($data)) echo $data; ?>">
                                                    <div class="input-group-btn">
                                                        <button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                            <?php }
                                }
                            } ?>
                        </div>
                    </div>

                    <div class="mb-3 row copy2 hide">
                        <div class="col-sm-10 ">
                            <div class="control-group input-group input-grp removemore" style="margin-top:10px">
                                <input type="text" class="form-control">
                                <div class="input-group-btn">
                                    <button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label fw-bold"> Invoice No <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <?php
                            $date = date('d');
                            $month = date('m');
                            $year = date('Y');
                            $id = 1;
                            if (isset($invoice_id->id) && $invoice_id->id != '') {
                                $id = $invoice_id->id + 1;
                            }

                            $form_data['invoice_no'] = 'AYE' . $month . $date . $year . '0' . $id;
                            ?>
                            <input type="text" readonly class="form-control create-input" name="invoice_no" id="invoice_no" value="<?php if (isset($form_data['invoice_no'])) echo $form_data['invoice_no']; ?>" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label fw-bold"> Date <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control create-input" name="invoice_date" id="invoice_date" value="<?php if (isset($form_data['invoice_date'])) echo $form_data['invoice_date']; ?>" required>
                        </div>
                    </div>
                    <button type="button" class="btn butn-submit text-white sbmtBtn" id="btn">Preview & Submit</button>
                </form>
            </div>
            
            <?php if (isset($form_data['id'])) { ?>
                <button id="generate-pdf" class="btn butn-submit text-white" id="btn" onclick="createPDF()">Generate PDF</button>
                <iframe id="download_content" src="http://localhost:8080/admin/preview_invoice/<?= $form_data['id'] ?>" width="100%" height="1000"></iframe>
                <!-- <embed id="download_content" src="http://localhost:8080/admin/preview_invoice/<?= $form_data['id'] ?>" type="application/pdf" height="720" width="100%" /> -->

            <?php } ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>


<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>

<?php if (session('error_message')) : ?>
    <script>
        toastr.error('<?= session('error_message') ?>');
    </script>
<?php endif; ?>
<script type="text/javascript">
    function createPDF() {
        var sTable = $('#download_content');
        // var sTable = document.getElementById('download_content');
        console.log(sTable.contents().find('html').html());
        // console.log(.outerHTML)

        var style = '<link type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.css" rel="stylesheet">';

        // CREATE A WINDOW OBJECT.
        var win = window.open('', '', 'height=700,width=700');

        win.document.write('<!DOCTYPE html>');
        // win.document.write('<title>Profile</title>');   // <title> FOR PDF HEADER.
        // win.document.write(style);          // ADD STYLE INSIDE THE HEAD TAG.
        // win.document.write('</head>');
        // win.document.write('<body>');
        // win.document.write(sTable);         // THE TABLE CONTENTS INSIDE THE BODY TAG.
        // win.document.write('</body></html>');
        win.document.write(sTable.contents().find('html').html());
        win.print();    // PRINT THE CONTENTS.
        win.close(); 	// CLOSE THE CURRENT WINDOW.
    }
    $(document).ready(function() {
        

        <?php if (isset($form_data['id'])) { ?>
            $(".hide_show").show();
            $(".hide").hide();
        <?php  } else { ?>
            $(".hide").hide();
        <?php  } ?>


        $(".add-more").click(function() {
            var html = $(".copy:hidden").clone().removeClass('hide').removeAttr('style');
            html.find('input').attr('name', 'addmore[]');
            $('.input-container').append(html);
        });

        $(".add-more1").click(function() {
            var html = $(".copy1").html();
            // html.find('input').attr('name', 'amntmore[]');
            $(".after-add-more1").after(html);
        });

        $(".add-more2").click(function() {
            var html = $(".copy2").html();
            // html.find('copy2').attr('name', 'qntymore[]');
            $(".after-add-more2").after(html);
        });


        $("body").on("click", ".remove", function() {
            $(this).parents(".control-group").remove();
            //   $(".removemore").remove();
        });

        $(".sbmtBtn").click(function(evt) {
            if ($('#invoice_form').valid()) {
                $('#sbmtBtn').attr("disabled", true);
                $('#invoice_form').submit();
            }
        });

    });
</script>
<?= $this->endSection() ?>