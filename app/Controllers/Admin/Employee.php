<?php

namespace App\Controllers\Admin;


use App\Controllers\BaseController;

class Employee extends BaseController
{

    public function __construct()
    {

        $this->session = session();
        $this->LmsModel = new \App\Models\LmsModel();
    }

    public function index()
    {
        if ($this->checkSession('A') != '') {
            $this->data['title'] = 'Employee List';
            echo view(ADMIN_PATH . '/employee/list', $this->data);
        } else {
            $this->session->setFlashdata('error_message', 'Please login!!!');
            return redirect()->to('/' . ADMIN_PATH);
        }
    }

    public function list_ajax($returnType = 'json')
    {
        $draw = $this->request->getPostGet('draw');
        $row_start = $this->request->getPostGet('start');
        $rowperpage = $this->request->getPostGet('length'); // Rows display per page

        $columnIndex = 0;
        if (isset($this->request->getPostGet('order')[0]['column'])) {
            $columnIndex = $this->request->getPostGet('order')[0]['column']; // Column index
        }
        $sortField = ''; // Column name
        if (isset($this->request->getPostGet('columns')[$columnIndex]['data'])) {
            $sortField = $this->request->getPostGet('columns')[$columnIndex]['data'];
        }
        $columnIndex = 'asc';
        if (isset($this->request->getPostGet('order')[0]['dir'])) {
            $sortJob = $this->request->getPostGet('order')[0]['dir']; // asc or desc
        }
        $dtSearchKeyVal = '';
        if (isset($this->request->getPostGet('search')['value'])) {
            $dtSearchKeyVal = $this->request->getPostGet('search')['value']; // Search value
        }
        $likeArr = [];
        $condition = array('is_deleted' => '0');
        if ($dtSearchKeyVal != '') {
            $likeArr = array(
                'first_name' => trim($dtSearchKeyVal),
                'last_name' => strtolower(trim($dtSearchKeyVal)),
                'dob' => trim($dtSearchKeyVal),
                'phone' => trim($dtSearchKeyVal),
                'email' => trim($dtSearchKeyVal),
                // 'department_id'=> trim($dtSearchKeyVal),
                // 'role_id'=> trim($dtSearchKeyVal),
            );
        }

        $totCounts = $this->LmsModel->get_all_counts(EMPLOYEE_DETAILS, $condition, '', $likeArr);
        $sortArr = array('id' => -1);
        if ($sortField != '') {
            $sortArr = array($sortField => $sortJob);
        }

        $ajaxDataArr = $this->LmsModel->get_all_details(EMPLOYEE_DETAILS, $condition, $sortArr, $rowperpage, $row_start, $likeArr);

        $tblData = array();
        $position = 1;
        $x = 1;
        foreach ($ajaxDataArr->getResult() as $row) {
            $cond = ['id' => $row->role_id];
            $role_name = $this->LmsModel->get_all_details(EMPLOYEE_ROLE, $cond)->getRow();

            $cond2 = ['id' => $row->department_id];
            $dept_name = $this->LmsModel->get_all_details(DEPARTMENT_DETAILS, $cond2)->getRow();

            $rowId =  (string)$row->id;
            $disp_status = 'Inactive';
            $actTitle = 'Click to active';
            $mode = 1;
            $btnColr = 'btn-danger';
            if (isset($row->status) && $row->status == '1') {
                $disp_status = 'Active';
                $mode = 0;
                $btnColr = 'btn-success';
                $actTitle = 'Click to inactivate';
            }
            $statusTxt = $actTitle;
            $actionTxt = '';

            $actionTxt = '<a class="btn btn-icon text-info" href="/' . ADMIN_PATH . '/employee/view/' . (string)$rowId . '"><i class="fas fa-eye"></i></a>';

            $statusTxt =  '<a data-toggle="tooltip" data-original-title="' . $actTitle . '" class="stsconfirm" href="javascript:void(0);" data-row_id="' . $rowId . '" data-act_url="/' . ADMIN_PATH . '/employee/change-status" data-stsmode="' . $mode . '"> <button type="button" class="btn ' . $btnColr . ' btn-sm waves-effect waves-light">' . $disp_status . '</button></a>';

            $actionTxt .= '<a class="btn btn-icon " href="/' . ADMIN_PATH . '/employee/edit/' . (string)$rowId . '"><i class="fas fa-edit"></i></a>';


            $actionTxt .= '<a href="javascript:void(0);" class="delconfirm btn btn-icon text-danger" data-row_id="' . $rowId . '" data-act_url="/' . ADMIN_PATH . '/employee/delete"><i class="fas fa-trash-alt"></i></a>';

            $bg_txt =  '<button type="button" class="btn btn-info bg_check" data-act_url="/' . ADMIN_PATH . '/employee/get-background_dts"  data-emp_id="' . $rowId . '">Doc </button>';

            $tblData[] = array(
                // 'DT_RowId' => (string)$rowId,
                // 'checker_box' => '<input class="checkRows" name="checkbox_id[]" type="checkbox" value="' . $rowId . '">',
                's_no' => $x,
                'employeeid' => ucfirst($row->employeeid),
                'first_name' => ucfirst($row->first_name),
                'last_name' => ucfirst($row->last_name),
                'role_id' => ucfirst($role_name->role_name),
                'department_id' => ucfirst($dept_name->department_name),
                'dob' => $row->dob,
                'phone' => $row->phone,
                'email' => $row->email,
                // 'bg_check' => $bg_chk_txt,
                'bg_check' => $bg_txt,
                "status" =>  $statusTxt,
                "action" =>  $actionTxt
            );
            $x++;
        }
        $response = array(
            "status" => '1',
            "draw" => intval($draw),
            "iTotalRecords" => $totCounts,
            "iTotalDisplayRecords" => $totCounts,
            "aaData" => $tblData
        );
        $returnArr = $response;
        echo json_encode($returnArr);
    }

    public function get_background_dts()
    {
        $emp_id = (string)$this->request->getPostGet('emp_id');
        $cond = ['id' => $emp_id];
        $emp_dts = $this->LmsModel->get_all_details(EMPLOYEE_DETAILS, $cond)->getRow();
        $exp_type = $emp_dts->work_exp;
        $bg_chk_txt = '';
        if (isset($exp_type) && $exp_type == 'fresher') {
            $doc_10_chk = $doc_12_chk = $ug_doc_chk = $pg_doc_chk  = $aadhar_card_chk  = $pan_card_chk = $offer_letter_chk = $mail_verify_chk = $exp_resign_letter_chk = $pay_slip_chk = '';
            if (isset($emp_dts->doc_10) && $emp_dts->doc_10 == '1') {
                $doc_10_chk = 'checked="checked"';
            }
            if (isset($emp_dts->doc_12) && $emp_dts->doc_12 == '1') {
                $doc_12_chk = 'checked="checked"';
            }
            if (isset($emp_dts->ug_doc) && $emp_dts->ug_doc == '1') {
                $ug_doc_chk = 'checked="checked"';
            }
            if (isset($emp_dts->pg_doc) && $emp_dts->pg_doc == '1') {
                $pg_doc_chk = 'checked="checked"';
            }
            if (isset($emp_dts->aadhar_card) && $emp_dts->aadhar_card == '1') {
                $aadhar_card_chk = 'checked="checked"';
            }
            if (isset($emp_dts->pan_card) && $emp_dts->pan_card == '1') {
                $pan_card_chk = 'checked="checked"';
            }
            if (isset($emp_dts->offer_letter) && $emp_dts->offer_letter == '1') {
                $offer_letter_chk = 'checked="checked"';
            }
            if (isset($emp_dts->mail_verify) && $emp_dts->mail_verify == '1') {
                $mail_verify_chk = 'checked="checked"';
            }
            if (isset($emp_dts->exp_resign_letter) && $emp_dts->exp_resign_letter == '1') {
                $exp_resign_letter_chk = 'checked="checked"';
            }
            if (isset($emp_dts->pay_slip) && $emp_dts->pay_slip == '1') {
                $pay_slip_chk = 'checked="checked"';
            }
            $bg_chk_txt .= '<form id="back_c">
            <input type="hidden" id="id" class="form-control" name="id" value="' . $emp_id . '"><label class=""> Education Qualification doc </label>
                    <div class="row">
                    <div class="col-3">
                    <label class="">10th</label>
                    <div class="form-check form-switch">
                    <input class="form-check-input 10_doc" type="checkbox"  name="10_doc" ' . $doc_10_chk . ' value="1">
                    </div>
                </div>
                <div class="col-3">
                    <label class="">12th</label>
                    <div class="form-check form-switch ">
                    <input class="form-check-input 12_doc" type="checkbox"  name="12_doc" ' . $doc_12_chk . ' value="1">
                </div>
                    </div>
                    <div class="col-3">
                    <label class="">UG</label>
                    <div class="form-check form-switch ">
                    <input class="form-check-input ug_doc" type="checkbox"  name="ug_doc" ' . $ug_doc_chk . ' value="1">
                </div>
                    </div>
                    <div class="col-3">
                    <label class="">PG</label>
                    <div class="form-check form-switch ">
                    <input class="form-check-input pg_doc" type="checkbox" name="pg_doc" ' . $pg_doc_chk . ' value="1">
                </div>
                    </div>
                    </div>
                    <hr>
                    <label class="">Address Proof(Aadhar Card)</label>
                    <div class="row">
                    <div class="col-3">
                    <div class="form-check form-switch ">
                    <input class="form-check-input aadhar_card" type="checkbox"  name="aadhar_card" ' . $aadhar_card_chk . 'value="1">
                    </div>
                </div></div>
                <hr>
                <label class="">ID Proof</label>
                    <div class="row">
                    <div class="col-4">
                    <label class="">Pan Card</label>
                    <div class="form-check form-switch ">
                    <input class="form-check-input pan_card" type="checkbox"  name="pan_card" ' . $pan_card_chk . 'value="1">
                    </div>
                </div>
                </div>
                <button class="d-none" id="updateBgBtn" type="submit">Update post</button>
                </form>';
        } else if ($exp_type == 'experience') {
            $doc_10_chk = $doc_12_chk = $ug_doc_chk = $pg_doc_chk  = $aadhar_card_chk  = $pan_card_chk = $offer_letter_chk = $mail_verify_chk = $exp_resign_letter_chk = $pay_slip_chk = '';
            if (isset($emp_dts->doc_10) && $emp_dts->doc_10 == '1') {
                $doc_10_chk = 'checked="checked"';
            }
            if (isset($emp_dts->doc_12) && $emp_dts->doc_12 == '1') {
                $doc_12_chk = 'checked="checked"';
            }
            if (isset($emp_dts->ug_doc) && $emp_dts->ug_doc == '1') {
                $ug_doc_chk = 'checked="checked"';
            }
            if (isset($emp_dts->pg_doc) && $emp_dts->pg_doc == '1') {
                $pg_doc_chk = 'checked="checked"';
            }
            if (isset($emp_dts->aadhar_card) && $emp_dts->aadhar_card == '1') {
                $aadhar_card_chk = 'checked="checked"';
            }
            if (isset($emp_dts->pan_card) && $emp_dts->pan_card == '1') {
                $pan_card_chk = 'checked="checked"';
            }
            if (isset($emp_dts->offer_letter) && $emp_dts->offer_letter == '1') {
                $offer_letter_chk = 'checked="checked"';
            }
            if (isset($emp_dts->mail_verify) && $emp_dts->mail_verify == '1') {
                $mail_verify_chk = 'checked="checked"';
            }
            if (isset($emp_dts->exp_resign_letter) && $emp_dts->exp_resign_letter == '1') {
                $exp_resign_letter_chk = 'checked="checked"';
            }
            if (isset($emp_dts->pay_slip) && $emp_dts->pay_slip == '1') {
                $pay_slip_chk = 'checked="checked"';
            }
            $bg_chk_txt .= '<form id="back_c"><input type="hidden" id="id" class="form-control" name="id" value="' . $emp_id . '"><label class=""> Education Qualification doc </label>
                <div class="row">
                <div class="col-3">
                <label class="">10th</label>
                <div class="form-check form-switch">
                <input class="form-check-input 10_doc" type="checkbox"  name="10_doc" ' . $doc_10_chk . ' value="1">
                </div>
            </div>
            <div class="col-3">
                <label class="">12th</label>
                <div class="form-check form-switch ">
                <input class="form-check-input 12_doc" type="checkbox"  name="12_doc" ' . $doc_12_chk . ' value="1">
            </div>
                </div>
                <div class="col-3">
                <label class="">UG</label>
                <div class="form-check form-switch ">
                <input class="form-check-input ug_doc" type="checkbox"  name="ug_doc" ' . $ug_doc_chk . ' value="1">
            </div>
                </div>
                <div class="col-3">
                <label class="">PG</label>
                <div class="form-check form-switch ">
                <input class="form-check-input pg_doc" type="checkbox" name="pg_doc" ' . $pg_doc_chk . ' value="1">
            </div>
                </div>
                </div>
                <hr>
                <label class="">Address Proof(Aadhar Card)</label>
                <div class="row">
                <div class="col-3">
                <div class="form-check form-switch ">
                <input class="form-check-input aadhar_card" type="checkbox"  name="aadhar_card" ' . $aadhar_card_chk . 'value="1">
                </div>
            </div></div>
            <hr>
            <label class="">ID Proof</label>
                <div class="row">
                <div class="col-4">
                <label class="">Pan Card</label>
                <div class="form-check form-switch ">
                <input class="form-check-input pan_card" type="checkbox"  name="pan_card" ' . $pan_card_chk . 'value="1">
                </div>
            </div>
            <div class="col-4">
                <label class="">Offer Letter</label>
                <div class="form-check form-switch ">
                <input class="form-check-input offer_letter" type="checkbox"  name="offer_letter" ' . $offer_letter_chk . 'value="1">
                </div>
            </div>
            
            <div class="col-4">
            <label class="">Mail Verify</label>
            <div class="form-check form-switch ">
            <input class="form-check-input mail_verify" type="checkbox"  name="mail_verify" ' . $mail_verify_chk . ' value="1">
            </div>
        </div>
            </div>
            <div class="row">
            <div class="col-6">
                <label class="">Exp/Resign Letter</label>
                <div class="form-check form-switch ">
                <input class="form-check-input exp_resign_letter" type="checkbox"  name="exp_resign_letter" ' . $exp_resign_letter_chk . ' value="1">
                </div>
            </div>
        <div class="col-5">
            <label class="">Last 6 months payslip</label>
            <div class="form-check form-switch ">
            <input class="form-check-input pay_slip" type="checkbox"  name="pay_slip" ' . $pay_slip_chk . ' value="1">
            </div>
        </div>
            </div>
            <button class="d-none" id="updateBgBtn" type="submit">Update post</button>
            </form>';
        }
        // echo $bg_chk_txt;die;
        echo json_encode($bg_chk_txt);
        exit;
    }

    public function update_bg_check()
    {
        // echo"<pre>";print_R($_POST);die;
        $emp_id = (string)$this->request->getPostGet('id');
        $doc_10 = (string)$this->request->getPostGet('10_doc');
        $doc_12 = (string)$this->request->getPostGet('12_doc');
        $ug_doc = (string)$this->request->getPostGet('ug_doc');
        $pg_doc = (string)$this->request->getPostGet('pg_doc');
        $aadhar_card = (string)$this->request->getPostGet('aadhar_card');
        $pan_card = (string)$this->request->getPostGet('pan_card');
        $offer_letter = (string)$this->request->getPostGet('offer_letter');
        $mail_verify = (string)$this->request->getPostGet('mail_verify');
        $exp_resign_letter = (string)$this->request->getPostGet('exp_resign_letter');
        $pay_slip = (string)$this->request->getPostGet('pay_slip');

        $dataArr = array(
            'doc_10' => $doc_10,
            'doc_12' => $doc_12,
            'ug_doc' => $ug_doc,
            'pg_doc' => $pg_doc,
            'aadhar_card' => $aadhar_card,
            'pan_card' => $pan_card,
            'offer_letter' => $offer_letter,
            'mail_verify' => $mail_verify,
            'exp_resign_letter' => $exp_resign_letter,
            'pay_slip' => $pay_slip,
        );
        $condition = array('id' => $emp_id);
        // echo"<pre>";print_r($condition);die;
        $this->LmsModel->update_details(EMPLOYEE_DETAILS, $dataArr, $condition);
        $response = 'success';
        echo json_encode($response);
        exit;
    }

    public function add_edit($id = "")
    {
        if ($this->checkSession('A') != '') {
            $uri = service('uri');
            $id = $uri->getSegment(4);
            $this->data['role_opt'] = $this->LmsModel->get_selected_fields(EMPLOYEE_ROLE, ['status' => '1', 'is_deleted' => '0'], ['id', 'role_name'])->getResult();
            $this->data['dept_opt'] = $this->LmsModel->get_selected_fields(DEPARTMENT_DETAILS, ['status' => '1', 'is_deleted' => '0'], ['id', 'department_name'])->getResult();
            if ($id != '') {
                $condition = array('is_deleted' => '0', 'id' => $id);
                $this->data['info'] = $this->LmsModel->get_selected_fields(EMPLOYEE_DETAILS, $condition)->getRow();
                if (!empty($this->data['info'])) {
                    $this->data['title'] = 'Edit Employee Details';
                } else {
                    $this->session->setFlashdata('error_message', 'Couldnot find the Employee');
                    return redirect()->route(ADMIN_PATH . '/employee/list');
                }
            } else {
                $this->data['title'] = 'Add Employee';
            }
            echo view(ADMIN_PATH . '/employee/add_edit', $this->data);
        } else {
            $this->session->setFlashdata('error_message', 'Please login!!!');
            return redirect()->to('/' . ADMIN_PATH);
        }
    }



    public function form_validation()
    {
        if ($this->checkSession('A') != '') {
            $type = (string)$this->request->getPostGet('type');
            $id = (string)$this->request->getPostGet('id');


            $first_name = (string)$this->request->getPostGet('first_name');
            $last_name = (string)$this->request->getPostGet('last_name');
            $date_of_birth = (string)$this->request->getPostGet('date_of_birth');
            $phone_number = (string)$this->request->getPostGet('phone_number');
            $email = (string)$this->request->getPostGet('email');
            $address = (string)$this->request->getPostGet('address');
            $pin_code = (string)$this->request->getPostGet('pin_code');
            $city = (string)$this->request->getPostGet('city');
            $state = (string)$this->request->getPostGet('state');
            $blood_group = (string)$this->request->getPostGet('blood_group');
            $aadhar_no = (string)$this->request->getPostGet('aadhar_no');
            $pan_no = (string)$this->request->getPostGet('pan_no');
            $password = (string)$this->request->getPostGet('password');
            $confirmpassword = (string)$this->request->getPostGet('confirmpassword');
            $role_id = (string)$this->request->getPostGet('role_id');
            $department_id = (string)$this->request->getPostGet('department_id');
            $employeeid = (string)$this->request->getPostGet('employeeid');
            $gender = (string)$this->request->getPostGet('gender');
            $emp_type = (string)$this->request->getPostGet('emp_type');

            $relationship = (string)$this->request->getPostGet('relationship');
            $r_name = (string)$this->request->getPostGet('r_name');
            $r_phone = (string)$this->request->getPostGet('r_phone');
            $r_email = (string)$this->request->getPostGet('r_email');
            $r_address = (string)$this->request->getPostGet('r_address');
            $pin_code = (string)$this->request->getPostGet('pin_code');
            $city = (string)$this->request->getPostGet('city');
            $state = (string)$this->request->getPostGet('state');

            $fresher_experience = (string)$this->request->getPostGet('fresher_experience');
            $file = $this->request->getFile('cv_resume');
            // $file = $this->request->getFile('cv_resume');
            $notes = (string)$this->request->getPostGet('notes');
            $status = (string)$this->request->getPostGet('status');


            if ($id == '') {
                $validation = \Config\Services::validation();
                if (isset($type) && $type == 'step_1') {
                    $validation->setRules(
                        [
                            'first_name' => 'required',
                            'last_name' => 'required',
                            'date_of_birth' => 'required',
                            'phone_number' => 'required|max_length[10]|min_length[10]|is_unique[employee_details.phone]',
                            'email' => 'required|valid_email|is_unique[employee_details.email]',
                            'address' => 'required',
                            'pin_code' => 'required',
                            'city' => 'required',
                            'state' => 'required',
                            'blood_group' => 'required',
                            'aadhar_no' => 'required',
                            'pan_no' => 'required',
                            'password' => 'required|max_length[25]|min_length[8]',
                            'confirmpassword' => 'required|max_length[255]|matches[password]',
                            'department_id' => 'required',
                            'role_id' => 'required',
                            'gender' => 'required',
                            'emp_type' => 'required',
                            'employeeid' => 'required|is_unique[employee_details.employeeid]',
                        ],
                        [   // Errors
                            'first_name' => [
                                'required' => 'This field is required.',
                            ],
                            'last_name' => [
                                'required' => 'This field is required.',
                            ],
                            'date_of_birth' => [
                                'required' => 'This field is required.',
                            ],
                            'phone_number' => [
                                'required' => 'This field is required.',
                            ],
                            'email' => [
                                'required' => 'This field is required.',
                            ],
                            'address' => [
                                'required' => 'This field is required.',
                            ],
                            'pin_code' => [
                                'required' => 'This field is required.',
                            ],
                            'city' => [
                                'required' => 'This field is required.',
                            ],
                            'state' => [
                                'required' => 'This field is required.',
                            ],
                            'blood_group' => [
                                'required' => 'This field is required.',
                            ],
                            'aadhar_no' => [
                                'required' => 'This field is required.',
                            ],
                            'pan_no' => [
                                'required' => 'This field is required.',
                            ],
                            'role_id' => [
                                'required' => 'This field is required.',
                            ],
                            'department_id' => [
                                'required' => 'This field is required.',
                            ],
                            'gender' => [
                                'required' => 'This field is required.',
                            ],
                            'emp_type' => [
                                'required' => 'This field is required.',
                            ],
                        ]
                    );
                    if (!$validation->withRequest($this->request)->run()) {
                        $errors = $validation->getErrors();
                        $returnArr['status'] = '0';
                        $returnArr['response'] = $errors;
                        return $this->response->setStatusCode(422)->setJSON($returnArr);
                    } else {
                        $returnArr['status'] = '1';
                        $returnArr['response'] = 'success';
                    }
                } else if ($type == 'step_2') {
                    $validation->setRules(
                        [
                            'relationship' => 'required',
                            'r_name' => 'required',
                            'r_phone' => 'required|max_length[10]|min_length[10]|is_unique[employee_details.r_phone]',
                            // 'r_email' => 'required|valid_email|is_unique[employee_details.r_email]',
                            'r_address' => 'required',

                        ],
                        [
                            'relationship' => [
                                'required' => 'This field is required.',
                            ],
                            'r_name' => [
                                'required' => 'This field is required.',
                            ],
                            'r_phone' => [
                                'required' => 'This field is required.',
                            ],
                            // 'r_email' => [
                            //     'required' => 'This field is required.',
                            // ],
                            'r_address' => [
                                'required' => 'This field is required.',
                            ],
                        ]
                    );
                    if (!$validation->withRequest($this->request)->run()) {
                        $errors = $validation->getErrors();
                        $returnArr['status'] = '0';
                        $returnArr['response'] = $errors;
                        return $this->response->setStatusCode(422)->setJSON($returnArr);
                    } else {
                        $returnArr['status'] = '1';
                        $returnArr['response'] = 'success';
                    }
                } else {
                    if ($status == '') {
                        $status = 'off';
                    }
                    // $validation = \Config\Services::validation();
                    $validation->setRules(
                        [
                            'fresher_experience' => 'required',
                            'cv_resume' => 'required',
                            'notes' => 'required',
                        ],
                        [   // Errors
                            'fresher_experience' => [
                                'required' => 'This field is required.',
                            ],
                            'cv_resume' => [
                                'required' => 'This field is required.',
                            ],
                            'notes' => [
                                'required' => 'This field is required.',
                            ],
                        ]
                    );
                    if (!$validation->withRequest($this->request)->run()) {
                        $errors = $validation->getErrors();
                        $returnArr['status'] = '0';
                        $returnArr['response'] = $errors;
                        return $this->response->setStatusCode(422)->setJSON($returnArr);
                    } else {
                        if ($status == 'on') {
                            $status = '1';
                        } else {
                            $status = '0';
                        }
                        $dataArr = array(
                            'first_name' => $first_name,
                            'last_name' => $last_name,
                            'dob' => $date_of_birth,
                            'phone' => $phone_number,
                            'email' => $email,
                            'address' => $address,
                            'pin_code' => $pin_code,
                            'city' => $city,
                            'state' => $state,
                            'blood_grp' => $blood_group,
                            'aadhar_no' => $aadhar_no,
                            'pan_no' => $pan_no,
                            'relationship' => $relationship,
                            'r_name' => $r_name,
                            'r_phone' => $r_phone,
                            'r_email' => $r_email,
                            'r_address' => $r_address,
                            'work_exp' => $fresher_experience,
                            'department_id' => $department_id,
                            'role_id' => $role_id,
                            'status' => $status,
                            'notes' => $notes,
                            'is_deleted' => '0',
                            'employeeid' => $employeeid,
                            'gender' => $gender,
                            'employment_type' => $emp_type,
                        );
                        $dataArr['password'] = password_hash($password, PASSWORD_DEFAULT);
                        if ($file !== null) {
                            if ($file->isValid() && !$file->hasMoved()) {
                                $newName = $file->getRandomName();
                                $file->move(WRITEPATH . EMPLOYEE_RESUME_DOC_PATH, $newName);
                                $dataArr['resume'] = $file->getName();
                            } else {
                                echo 'Upload failed.';
                            }
                        } else {
                        }
                        $returnArr['status'] = '1';
                        $returnArr['response'] = 'success';
                        $this->LmsModel->simple_insert(EMPLOYEE_DETAILS, $dataArr);
                        $redirectUrl = site_url('/' . ADMIN_PATH . '/employee/list');
                        return $this->response->setJSON(['redirect' => $redirectUrl]);
                    }
                }
            } else {
                $validation = \Config\Services::validation();
                if (isset($type) && $type == 'step_1') {
                    $validation->setRules(
                        [
                            'first_name' => 'required',
                            'last_name' => 'required',
                            'date_of_birth' => 'required',
                            'phone_number' => 'required|max_length[10]|min_length[10]|is_unique[employee_details.phone,id,' . $id . ']',
                            'email' => 'required|valid_email|is_unique[employee_details.email,id,' . $id . ']',
                            'address' => 'required',
                            'pin_code' => 'required',
                            'city' => 'required',
                            'state' => 'required',
                            'blood_group' => 'required',
                            'aadhar_no' => 'required',
                            'pan_no' => 'required',
                            'department_id' => 'required',
                            'gender' => 'required',
                            'emp_type' => 'required',
                            'role_id' => 'required',
                            'employeeid' => 'required|is_unique[employee_details.employeeid,id,' . $id . ']',
                            // 'password' => 'required|max_length[25]|min_length[8]',
                            // 'confirmpassword' => 'required|max_length[255]|matches[password]',
                        ],
                        [   // Errors
                            'first_name' => [
                                'required' => 'This field is required.',
                            ],
                            'last_name' => [
                                'required' => 'This field is required.',
                            ],
                            'date_of_birth' => [
                                'required' => 'This field is required.',
                            ],
                            'phone_number' => [
                                'required' => 'This field is required.',
                            ],
                            'email' => [
                                'required' => 'This field is required.',
                            ],
                            'address' => [
                                'required' => 'This field is required.',
                            ],
                            'pin_code' => [
                                'required' => 'This field is required.',
                            ],
                            'city' => [
                                'required' => 'This field is required.',
                            ],
                            'state' => [
                                'required' => 'This field is required.',
                            ],
                            'blood_group' => [
                                'required' => 'This field is required.',
                            ],
                            'aadhar_no' => [
                                'required' => 'This field is required.',
                            ],
                            'pan_no' => [
                                'required' => 'This field is required.',
                            ],
                            'role_id' => [
                                'required' => 'This field is required.',
                            ],
                            'department_id' => [
                                'required' => 'This field is required.',
                            ],
                            'gender' => [
                                'required' => 'This field is required.',
                            ],
                            'emp_type' => [
                                'required' => 'This field is required.',
                            ],
                        ]
                    );
                    if (!$validation->withRequest($this->request)->run()) {
                        $errors = $validation->getErrors();
                        $returnArr['status'] = '0';
                        $returnArr['response'] = $errors;
                        return $this->response->setStatusCode(422)->setJSON($returnArr);
                    } else {
                        $returnArr['status'] = '1';
                        $returnArr['response'] = 'success';
                    }
                } else if ($type == 'step_2') {
                    $validation->setRules(
                        [
                            'relationship' => 'required',
                            'r_name' => 'required',
                            'r_phone' => 'required|max_length[10]|min_length[10]|is_unique[employee_details.r_phone,id,' . $id . ']',
                            // 'r_email' => 'required|valid_email|is_unique[employee_details.r_email,id,' . $id . ']',
                            'r_address' => 'required',

                        ],
                        [
                            'relationship' => [
                                'required' => 'This field is required.',
                            ],
                            'r_name' => [
                                'required' => 'This field is required.',
                            ],
                            'r_phone' => [
                                'required' => 'This field is required.',
                            ],
                            // 'r_email' => [
                            //     'required' => 'This field is required.',
                            // ],
                            'r_address' => [
                                'required' => 'This field is required.',
                            ],
                        ]
                    );
                    if (!$validation->withRequest($this->request)->run()) {
                        $errors = $validation->getErrors();
                        $returnArr['status'] = '0';
                        $returnArr['response'] = $errors;
                        return $this->response->setStatusCode(422)->setJSON($returnArr);
                    } else {
                        $returnArr['status'] = '1';
                        $returnArr['response'] = 'success';
                    }
                } else {
                    $validation->setRules(
                        [
                            'fresher_experience' => 'required',
                            // 'cv_resume' => 'required',
                            'notes' => 'required',
                        ],
                        [   // Errors
                            'fresher_experience' => [
                                'required' => 'This field is required.',
                            ],
                            'cv_resume' => [
                                'required' => 'This field is required.',
                            ],
                            'notes' => [
                                'required' => 'This field is required.',
                            ],
                        ]
                    );
                    if (!$validation->withRequest($this->request)->run()) {
                        $errors = $validation->getErrors();
                        $returnArr['status'] = '0';
                        $returnArr['response'] = $errors;
                        return $this->response->setStatusCode(422)->setJSON($returnArr);
                    } else {
                        if ($status == 'on') {
                            $status = '1';
                        } else {
                            $status = '0';
                        }
                        $dataArr = array(
                            'first_name' => $first_name,
                            'last_name' => $last_name,
                            'dob' => $date_of_birth,
                            'phone' => $phone_number,
                            'email' => $email,
                            'address' => $address,
                            'pin_code' => $pin_code,
                            'city' => $city,
                            'state' => $state,
                            'blood_grp' => $blood_group,
                            'aadhar_no' => $aadhar_no,
                            'pan_no' => $pan_no,
                            'relationship' => $relationship,
                            'r_name' => $r_name,
                            'r_phone' => $r_phone,
                            'r_email' => $r_email,
                            'r_address' => $r_address,
                            'work_exp' => $fresher_experience,
                            'department_id' => $department_id,
                            'role_id' => $role_id,
                            'status' => $status,
                            'notes' => $notes,
                            'is_deleted' => '0',
                            'employeeid' => $employeeid,
                            'gender' => $gender,
                            'employment_type' => $emp_type,
                        );
                        // $dataArr['password'] = password_hash($password, PASSWORD_DEFAULT);
                        if ($file !== null) {
                            if ($file->isValid() && !$file->hasMoved()) {
                                $newName = $file->getRandomName();
                                $file->move(WRITEPATH . EMPLOYEE_RESUME_DOC_PATH, $newName);
                                $dataArr['resume'] = $file->getName();
                            } else {
                                echo 'Upload failed.';
                            }
                        } else {
                        }
                        $returnArr['status'] = '1';
                        $returnArr['response'] = 'success';

                        $condition = array('id' => $id);
                        $this->LmsModel->update_details(EMPLOYEE_DETAILS, $dataArr, $condition);
                        $redirectUrl = site_url('/' . ADMIN_PATH . '/employee/list');
                        return $this->response->setJSON(['redirect' => $redirectUrl]);
                    }
                }
            }
            echo json_encode($returnArr);
            exit;
        } else {
            $this->session->setFlashdata('error_message', 'Please login!!!');
            return redirect()->to('/' . ADMIN_PATH);
        }
    }

    public function update_status()
    {
        if ($this->checkSession('A') != '') {
            $returnArr['status'] = '0';
            $returnArr['response'] = 'Failed to updated, Please try again';
            if ($this->checkSession('A') == '') {
                $returnArr['status'] = '00';
                $returnArr['response'] = 'Session has been timed out, Please login again and try.';
            } else {
                $mode = $this->request->getPostGet('mode');
                // echo $mode;die;
                $id = $this->request->getPostGet('record_id');
                $status = ($mode == '0') ? '0' : '1';
                $newdata = array('status' => $status);
                $condition = array('id' => $id);
                $this->LmsModel->update_details(EMPLOYEE_DETAILS, $newdata, $condition);
                $returnArr['status'] = '1';
                $returnArr['response'] = 'Employee Status Changed Successfully';
            }
            echo json_encode($returnArr);
            exit;
        } else {
            $this->session->setFlashdata('error_message', 'Please login!!!');
            return redirect()->to('/' . ADMIN_PATH);
        }
    }

    public function view($id = "")
    {
        if ($this->checkSession('A') != '') {
            $uri = service('uri');
            $id = $uri->getSegment(4);
            if ($id != '') {
                $condition = array('id' => $id, 'is_deleted' => '0');
                $this->data['empDetails'] = $this->LmsModel->get_all_details(EMPLOYEE_DETAILS, $condition)->getRow();
                $cond = ['id' => $this->data['empDetails']->role_id];
                $this->data['roleDetails'] = $this->LmsModel->get_all_details(EMPLOYEE_ROLE, $cond)->getRow();
                $cond2 = ['id' => $this->data['empDetails']->department_id];
                $this->data['deptDetails'] = $this->LmsModel->get_all_details(DEPARTMENT_DETAILS, $cond2)->getRow();
                if (!empty($this->data['empDetails'])) {
                    $this->data['title'] = 'Employee view';
                    echo view(ADMIN_PATH . '/employee/view', $this->data);
                } else {
                    $this->session->setFlashdata('error_message', 'Couldnot find the Employee');
                    // $this->setFlashMessage('error', 'Couldn\'t find the subadmin');
                    return redirect()->route(ADMIN_PATH . '/employee/list');
                }
            } else {
                $this->session->setFlashdata('error_message', 'Couldnot find the Employee');
                // $this->setFlashMessage('error', 'Couldn\'t find the subadmin');
                return redirect()->route(ADMIN_PATH . '/employee/list');
            }
        } else {
            $this->session->setFlashdata('error_message', 'Please login!!!');
            return redirect()->to('/' . ADMIN_PATH);
        }
    }

    public function delete()
    {
        if ($this->checkSession('A') != '') {
            $returnArr['status'] = '0';
            $returnArr['response'] = 'Failed to delete, Please try again';
            if ($this->checkSession('A') == '') {
                $returnArr['status'] = '00';
                $returnArr['response'] = 'Session has been timed out, Please login again and try.';
            } else {
                $record_id = $this->request->getPostGet('record_id');
                if (isset($record_id)) {
                    $this->LmsModel->isDelete(EMPLOYEE_DETAILS, 'id', TRUE);
                    // $this->setFlashMessage('success', 'Lms deleted successfully');
                    $returnArr['status'] = '1';
                    $returnArr['response'] = 'Record Deleted Successfully';
                }
                // $this->LmsModel->commonDelete(LMS, array('id' => $record_id));
            }

            echo json_encode($returnArr);
            exit;
        } else {
            $this->session->setFlashdata('error_message', 'Please login!!!');
            return redirect()->to('/' . ADMIN_PATH);
        }
    }

    public function showFile()
    {
        $uri = service('uri');
        $filename = $uri->getSegment(4);

        helper("filesystem");
        $path = WRITEPATH . 'uploads/employee_cv_doc/';

        $fullpath = $path . $filename;
        $file = new \CodeIgniter\Files\File($fullpath, true);
        $binary = readfile($fullpath);
        return $this->response
            ->setHeader('Content-Type', $file->getMimeType())
            ->setHeader('Content-disposition', 'inline; filename="' . $file->getBasename() . '"')
            ->setStatusCode(200)
            ->setBody($binary);
    }

    public function get_dept_opt_ajax()
    {
        // echo"xg";die;
        $role_id = (string)$this->request->getPostGet('role_id');
        if (isset($role_id) && $role_id != '') {
            $cond = ['id' => $role_id];
            $roleDetails = $this->LmsModel->get_all_details(EMPLOYEE_ROLE, $cond)->getRow();
            $cond2 = ['id' => $roleDetails->department_id, 'is_deleted' => 0, 'status' => 1];
            $deptDetails = $this->LmsModel->get_all_details(DEPARTMENT_DETAILS, $cond2)->getRow();
            //    echo"<pre>"; print_r($deptDetails);die;
            $html = '';
            if (!empty($deptDetails)) {
                $html .= '<option value="' . $deptDetails->id . ' ">
 ' . ucfirst($deptDetails->department_name) . '
</option>';
            } else {
                $html .= '<option value="">No Record</option>';
            }
        } else {
            $html['status'] = '0';
            $html['response'] = 'Failed to get Department Option';
        }
        echo json_encode($html);
        exit;
    }
}
