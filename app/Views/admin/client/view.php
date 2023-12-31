<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid mt-4">
    <div class="card create-box">
        <div class="card-body">
            <div class="row">
                <div class="col-md-11">
                    <h3><?= $title; ?></h3>
                    <ol class="breadcrumb p-0 m-0">
                        <li class="breadcrumb-item bread-home"><a href="<?= '/' . ADMIN_PATH . '/dashboard' ?>"><i class="fa fa-home me-0" aria-hidden="true"></i></a></li>
                        <li class="breadcrumb-item">
                            <a class="text-decoration-none" href="<?= '/' . ADMIN_PATH . '/client/list' ?>">Client</a>
                        </li>
                        <?php if (isset($clientDetails)) { ?>
                            <li class="breadcrumb-item">
                                <?php echo $clientDetails->first_name; ?>
                            </li>
                        <?php } ?>
                        <li class="breadcrumb-item active">
                            <?php echo 'view'; ?>
                        </li>
                    </ol>
                    <hr>
                </div>
            </div>


            <div class="table-responsive">
                <table class="table table-bordered m-b-0">
                    <tbody>
                        <tr>
                            <th scope="row"> Name </th>
                            <td><?php if (isset($clientDetails->first_name)) echo $clientDetails->first_name . ' ' . $clientDetails->last_name; ?></td>
                        </tr>
                        <tr>
                            <th scope="row"> Email </th>
                            <td><?php if (isset($clientDetails->email)) echo  ucfirst($clientDetails->email);   ?></td>
                        </tr>
                        <tr>
                            <th scope="row"> Phone </th>
                            <td><?php if (isset($clientDetails->phone)) echo ucfirst($clientDetails->phone); ?></td>
                        </tr>
                        <tr>
                            <th scope="row"> Address </th>
                            <td><?php if (isset($clientDetails->address)) echo ucfirst(wordwrap($clientDetails->address, 40, "<br />\n")); ?></td>
                        </tr>
                        <tr>
                            <th scope="row"> Company Name </th>
                            <td><?php if (isset($clientDetails->company_name)) echo ucfirst($clientDetails->company_name); ?></td>
                        </tr>
                        <tr>
                            <th scope="row"> Bank Name </th>
                            <td><?php if (isset($clientDetails->bank_name)) echo strtoupper($clientDetails->bank_name); ?></td>
                        </tr>
                        <tr>
                            <th scope="row"> Branch Name </th>
                            <td><?php if (isset($clientDetails->branch_name)) echo ucfirst($clientDetails->branch_name); ?></td>
                        </tr>
                        <tr>
                            <th scope="row"> Account Number </th>
                            <td><?php if (isset($clientDetails->acc_no)) echo ucfirst($clientDetails->acc_no); ?></td>
                        </tr>
                        <tr>
                            <th scope="row"> IFSC Code </th>
                            <td><?php if (isset($clientDetails->ifsc_code)) echo strtoupper($clientDetails->ifsc_code); ?></td>
                        </tr>
                        <tr>
                            <th scope="row"> Status </th>
                            <td><?php if (isset($clientDetails->status) && $clientDetails->status == 1) { ?><span class="btn btn-success"><?php echo 'Active'; ?></span><?php } else { ?><span class="btn btn-danger"><?php echo 'In Active'; ?></span><?php } ?></td>
                        </tr>
                        <tr>
                            <th scope="row"> Created At </th>
                            <td><?php if (isset($clientDetails->created_at)) echo date('d-m-Y h:i:s A', strtotime($clientDetails->created_at)); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>


<?= $this->endSection() ?>