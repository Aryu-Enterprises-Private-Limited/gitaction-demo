<?php

namespace App\Controllers\Admin;


use App\Controllers\BaseController;

class Gst extends BaseController
{

    public function __construct()
    {
        $this->session = session();
        $this->LmsModel = new \App\Models\LmsModel();
    }

    public function index()
    {
        if ($this->checkSession('A') != '') {
            $this->data['title'] = 'GST List';
            $this->data['alert_msg'] = $this->LmsModel->get_selected_fields(REMINDER_ALERT, ['status' => '1', 'is_deleted' => '0'], ['id', 'alert_name'])->getResult();
            echo view(ADMIN_PATH . '/gst/list', $this->data);
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
        $year = $this->request->getPostGet('year');

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
                'ref_no' => trim($dtSearchKeyVal),
                'filed_date' => trim($dtSearchKeyVal),
                'month' => trim($dtSearchKeyVal),
            );
        }
        if (isset($year) && !empty($year)) {
            if (!empty($year)) {
                $condition['filed_date'] = $year;
            }
        }

        $totCounts = $this->LmsModel->get_all_counts(GST_DETAILS, $condition, '', $likeArr);
        $sortArr = array('id' => -1);
        if ($sortField != '') {
            $sortArr = array($sortField => $sortJob);
        }
        $ajaxDataArr = $this->LmsModel->get_all_details(GST_DETAILS, $condition, $sortArr, $rowperpage, $row_start, $likeArr);


        $tblData = array();
        $x =1;
        foreach ($ajaxDataArr->getResult() as $row) {
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

            $actionTxt = '<a class="btn btn-icon text-info" href="/' . ADMIN_PATH . '/gst/view/' . (string)$rowId . '"><i class="fas fa-eye"></i></a>';

            $statusTxt =  '<a data-toggle="tooltip" data-original-title="' . $actTitle . '" class="stsconfirm" href="javascript:void(0);" data-row_id="' . $rowId . '" data-act_url="/' . ADMIN_PATH . '/gst/change-status" data-stsmode="' . $mode . '"> <button type="button" class="btn ' . $btnColr . ' btn-sm waves-effect waves-light">' . $disp_status . '</button></a>';

            $actionTxt .= '<a class="btn btn-icon " href="/' . ADMIN_PATH . '/gst/edit/' . (string)$rowId . '"><i class="fas fa-edit"></i></a>';


            $actionTxt .= '<a href="javascript:void(0);" class="delconfirm btn btn-icon text-danger" data-row_id="' . $rowId . '" data-act_url="/' . ADMIN_PATH . '/gst/delete"><i class="fas fa-trash-alt"></i></a>';


            $tblData[] = array(
                // 'DT_RowId' => (string)$rowId,
                // 'checker_box' => '<input class="checkRows" name="checkbox_id[]" type="checkbox" value="' . $rowId . '">',
                's_no' => $x,
                'month' => $row->month,
                'filed_date' => $row->filed_date,
                'ref_no' => $row->ref_no,
                'created_at' => $row->created_at,
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


    public function add_edit($id = "")
    {
        if ($this->checkSession('A') != '') {
            $uri = service('uri');
            $id = $uri->getSegment(4);
            if ($id != '') {
                $condition = array('is_deleted' => '0', 'id' => $id);
                $this->data['gst_info'] = $this->LmsModel->get_selected_fields(GST_DETAILS, $condition)->getRow();
                if (!empty($this->data['gst_info'])) {
                    $this->data['title'] = 'Edit GST';
                } else {
                    $this->session->setFlashdata('error_message', 'Couldnot find the Gst');
                    return redirect()->route(ADMIN_PATH . '/gst/list');
                }
            } else {
                $this->data['title'] = 'Add GST';
            }
            echo view(ADMIN_PATH . '/gst/add_edit', $this->data);
        } else {
            $this->session->setFlashdata('error_message', 'Please login!!!');
            return redirect()->to('/' . ADMIN_PATH);
        }
    }



    public function insertUpdate()
    {
        if ($this->checkSession('A') != '') {
            $filed_date = (string)$this->request->getPostGet('filed_date');
            $ref_no = (string)$this->request->getPostGet('ref_no');
            // $file = $this->request->getFile('gst_document');
            $file = $this->request->getFileMultiple('gst_document');
            $status = (string)$this->request->getPostGet('status');
            $id = (string)$this->request->getPostGet('id');
            $month = date("F", strtotime($filed_date));
            if ($status == '') {
                $status = 'off';
            }
            $fSubmit = FALSE;
            if ($month != '' && $filed_date != '' && $ref_no != '') {
                if ($status == 'on') {
                    $status = '1';
                } else {
                    $status = '0';
                }
                $dataArr = array(
                    'month' => $month,
                    'filed_date' => $filed_date,
                    'ref_no' => $ref_no,
                    'status' => $status,
                    'is_deleted' => '0',
                );

                if (isset($file) && !empty($file)) {
                    $commaSeparated =array();
                    $flag= 'success';
                    foreach($file as  $file){
                        if ($file->isValid() && !$file->hasMoved()) {
                            $newName = $file->getRandomName();
                            $file->move(WRITEPATH . GST_DOC_PATH, $newName);
                            $commaSeparated[] = $file->getName();
                            // $dataArr['gst_document'] = $file->getName();
                        }else{
                            $flag='fail';
                        }
                    }
                    if($flag != 'fail'){
                        $file_str = implode(',', $commaSeparated);
                        $dataArr['gst_document'] = $file_str;
                    }
                }
                
                if ($id == '') {
                    $this->LmsModel->simple_insert(GST_DETAILS, $dataArr);
                    $this->session->setFlashdata('success_message', 'GST added successfully.');
                    $fSubmit = TRUE;
                } else {
                    $condition = array('id' => $id);
                    $this->LmsModel->update_details(GST_DETAILS, $dataArr, $condition);
                    $this->session->setFlashdata('success_message', 'GST update successfully');
                    $fSubmit = TRUE;
                }
            } else {
                $this->session->setFlashdata('error_message', 'Form data is missing.');
            }
            if ($fSubmit) {
                $url = ADMIN_PATH . '/gst/list';
            } else {
                if ($id == '') $url = ADMIN_PATH . '/gst/add';
                else $url = ADMIN_PATH . '/gst/edit/' . $id;
            }
            return redirect()->to("$url");
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
                $id = $this->request->getPostGet('record_id');
                $status = ($mode == '0') ? '0' : '1';
                $newdata = array('status' => $status);
                $condition = array('id' => $id);
                $this->LmsModel->update_details(GST_DETAILS, $newdata, $condition);
                $returnArr['status'] = '1';
                $returnArr['response'] = 'GST Changed Successfully';
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
                $this->data['gst_details'] = $this->LmsModel->get_all_details(GST_DETAILS, $condition)->getRow();
                if (!empty($this->data['gst_details'])) {
                    $this->data['title'] = 'GST view';
                    echo view(ADMIN_PATH . '/gst/view', $this->data);
                } else {
                    $this->session->setFlashdata('error_message', 'Couldnot find the GST');
                    // $this->setFlashMessage('error', 'Couldn\'t find the subadmin');
                    return redirect()->route(ADMIN_PATH . '/gst/list');
                }
            } else {
                $this->session->setFlashdata('error_message', 'Couldnot find the GST');
                // $this->setFlashMessage('error', 'Couldn\'t find the subadmin');
                return redirect()->route(ADMIN_PATH . '/gst/list');
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
                    $this->LmsModel->isDelete(GST_DETAILS, 'id', TRUE);
                    $returnArr['status'] = '1';
                    $returnArr['response'] = 'GST Deleted Successfully';
                }
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
        $path = WRITEPATH . 'uploads/gst_doc/';

        $fullpath = $path . $filename;
        $file = new \CodeIgniter\Files\File($fullpath, true);
        $binary = readfile($fullpath);
        return $this->response
            ->setHeader('Content-Type', $file->getMimeType())
            ->setHeader('Content-disposition', 'inline; filename="' . $file->getBasename() . '"')
            ->setStatusCode(200)
            ->setBody($binary);
    }
    public function alert_close_ajax()
    {
        if ($this->checkSession('A') != '') {
            $returnArr['status'] = '0';
            $returnArr['response'] = 'fail';
            $alert_id = $this->request->getPostGet('id');
            if (isset($alert_id)) {
                $newdata = array('status' => '0');
                $condition = array('id' => $alert_id);
                $this->LmsModel->update_details(REMINDER_ALERT, $newdata, $condition);
                $returnArr['status'] = '1';
                $returnArr['response'] = 'Status changed';
            }
            echo json_encode($returnArr);
            exit;
        } else {
            $this->session->setFlashdata('error_message', 'Please login!!!');
            return redirect()->to('/' . ADMIN_PATH);
        }
    }
}
