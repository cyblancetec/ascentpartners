<?php

namespace App\DataTables;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Services\DataTable;

class SurveyReportDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @return \Yajra\Datatables\Engines\BaseEngine
     */
    public function dataTable()
    {
        return datatables()
            ->queryBuilder($this->query())
            ->addColumn('survey_title', function ($survey) {
                return $survey->title;
            })
            ->addColumn('average_of_esg_aspects', function ($survey) {
                $sql = 'SELECT sesg.esg_id, esgt.title
                        FROM survey_esgs as sesg, esg_translations as esgt
                        WHERE sesg.esg_id = esgt.esg_id
                        AND sesg.survey_id='.$survey->id.'
                        AND esgt.locale="en"';
                $survey_esgs = DB::select($sql);
                $str = '<ul>';
                foreach($survey_esgs as $survey_esg){
                    $str .= '<li>';
                        $str .= '<b>'.$survey_esg->title.'</b>';
                        $sql = 'SELECT seesg.esg_value, seesg.survey_type
                                FROM survey_entries as se
                                LEFT JOIN survey_entry_esgs as seesg ON se.id = seesg.survey_entry_id
                                WHERE se.survey_id='.$survey->id.'
                                AND seesg.esg_id="'.$survey_esg->esg_id.'"';
                        $survey_entries = DB::select($sql);
                        $company_esg_total = 0;
                        $stakeholder_esg_total = 0;
                        foreach($survey_entries as $survey_entry){
                            if($survey_entry->survey_type=='company'){
                                $company_esg_total = $company_esg_total + (int)$survey_entry->esg_value;
                            }else {
                                $stakeholder_esg_total = $stakeholder_esg_total + (int)$survey_entry->esg_value;
                            }
                        }
                        if($stakeholder_esg_total!=0){
                            $str .= ' Stakeholder: '.$stakeholder_esg_total;
                        }
                        if($company_esg_total!=0){
                            $str .= ' Company: '.$company_esg_total;
                        }
                        $total_value = $stakeholder_esg_total + $company_esg_total;
                        $max_value = count($survey_entries)*5;
                        $average = $total_value * 100 / $max_value;
                        $str .= ' Average: '. number_format($average,2).'%';
                    $str .= '</li>';
                }
                $str .= '</ul>';
                return $str;
            })
            ->editColumn('total_completed_survey', function ($survey) {
                $survey_entries = DB::select('SELECT COUNT(id) as total FROM survey_entries WHERE survey_entries.survey_id='.$survey->id);
                if(isset($survey_entries[0]->total)){
                    return $survey_entries[0]->total;
                }else{
                    return '';
                }
            })
            ->editColumn('top_5_esg_aspects_averages', function ($survey) {
                $array = array();
                $sql = 'SELECT sesg.esg_id, esgt.title
                        FROM survey_esgs as sesg, esg_translations as esgt
                        WHERE sesg.esg_id = esgt.esg_id
                        AND sesg.survey_id='.$survey->id.'
                        AND esgt.locale="en"';
                $survey_esgs = DB::select($sql);
                
                foreach($survey_esgs as $survey_esg){
                    $sql = 'SELECT seesg.esg_value, seesg.survey_type
                            FROM survey_entries as se
                            LEFT JOIN survey_entry_esgs as seesg ON se.id = seesg.survey_entry_id
                            WHERE se.survey_id='.$survey->id.'
                            AND seesg.esg_id="'.$survey_esg->esg_id.'"';
                    $survey_entries = DB::select($sql);
                    $company_esg_total = 0;
                    $stakeholder_esg_total = 0;
                    foreach($survey_entries as $survey_entry){
                        if($survey_entry->survey_type=='company'){
                            $company_esg_total = $company_esg_total + (int)$survey_entry->esg_value;
                        }else {
                            $stakeholder_esg_total = $stakeholder_esg_total + (int)$survey_entry->esg_value;
                        }
                    }
                    $total_value = $stakeholder_esg_total + $company_esg_total;
                    $max_value = count($survey_entries)*5;
                    $average = $total_value * 100 / $max_value;
                    $array = array_merge($array,array($survey_esg->title => number_format($average,2)));
                }
                arsort($array);
                $str = '<ul>';
                    $i = 1;
                    foreach ($array as $key => $value) {
                        if($i<6){
                            $str .= '<li>';
                                $str .= '<b>'.$key.'</b>: '.$value.'%';
                            $str .= '</li>';
                        }
                        $i++;
                    }
                $str .= '</ul>';
                return $str;
            })
            ->editColumn('total_of_stakeholder_identity', function ($survey) {
                $survey_entries = DB::select('SELECT SUM(sample_size) as total FROM survey_stakeholders WHERE survey_stakeholders.survey_id='.$survey->id);
                if(isset($survey_entries[0]->total)){
                    return $survey_entries[0]->total;
                }else{
                    return '';
                }
            })
            ->editColumn('percentage_of_stakeholder', function ($survey) {
                $sql = 'SELECT ssh.stakeholder_id, ssh.sample_size, sht.title
                        FROM survey_stakeholders as ssh, stakeholder_translations as sht
                        WHERE ssh.stakeholder_id = sht.stakeholder_id
                        AND ssh.survey_id='.$survey->id.'
                        AND sht.locale="en"';
                $survey_stakeholders = DB::select($sql);
                $str = '<ul>';
                foreach($survey_stakeholders as $survey_stakeholder){
                    $str .= '<li>';
                        $str .= '<b>'.$survey_stakeholder->title.'</b>';
                        $str .= '<ul>';
                            $sql = 'SELECT count(se.id) as total 
                                    FROM survey_entries as se
                                    LEFT JOIN survey_entry_stakeholders as ses ON se.id = ses.survey_entry_id
                                    WHERE se.survey_id='.$survey->id.'
                                    AND ses.stakeholder_id="'.$survey_stakeholder->stakeholder_id.'"';
                            $survey_entries = DB::select($sql);
                            $str .= '<li>Suggested: '.$survey_stakeholder->sample_size.'</li>';
                            $str .= '<li>Completed: '.$survey_entries[0]->total.'</li>';
                            $str .= '<li>Average: '.number_format($survey_entries[0]->total*100/$survey_stakeholder->sample_size, 2).'%</li>';
                        $str .= '</ul>';
                    $str .= '</li>';
                }
                $str .= '</ul>';
                return $str;
            })
            ->editColumn('comment', function ($survey) {
                $sql = 'SELECT ssh.stakeholder_id, sht.title
                        FROM survey_stakeholders as ssh, stakeholder_translations as sht
                        WHERE ssh.stakeholder_id = sht.stakeholder_id
                        AND ssh.survey_id='.$survey->id.'
                        AND sht.locale="en"';
                $survey_stakeholders = DB::select($sql);
                $str = '<ul>';
                foreach($survey_stakeholders as $survey_stakeholder){
                    $sql = 'SELECT ses.stakeholder_comment
                            FROM survey_entries as se
                            LEFT JOIN survey_entry_stakeholders as ses ON se.id = ses.survey_entry_id
                            WHERE se.survey_id='.$survey->id.'
                            AND ses.stakeholder_id="'.$survey_stakeholder->stakeholder_id.'"';
                    $survey_entries = DB::select($sql);
                    if(!empty($survey_entries)){
                        $i = 1;
                        foreach ($survey_entries as $survey_entry) {
                            if($survey_entry->stakeholder_comment!=''){
                                if($i==1){
                                    $str .= '<li>';
                                    $str .= '<b>'.$survey_stakeholder->title.'</b>';
                                }
                                $str .= '<br>'.$survey_entry->stakeholder_comment;
                                if($i==1){
                                    $str .= '</li>';
                                }
                                $i++;
                            }
                        }
                    }
                }
                $str .= '</ul>';
                return $str;
            })
            ->rawColumns(['average_of_esg_aspects', 'top_5_esg_aspects_averages', 'percentage_of_stakeholder', 'comment']);
    }

    /**
     * Get the query object to be processed by dataTables.
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Support\Collection
     */
    public function query()
    {
        $query = DB::table('surveys');

        return $this->applyScopes($query);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\Datatables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->columns($this->getColumns())
                    ->parameters([
                        'dom'          => 'Bfrtip',
                        'buttons'      => ['csv', 'excel', 'print', 'reset', 'reload'],
                    ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'survey_title',
            'average_of_esg_aspects',
            'total_completed_survey',
            'top_5_esg_aspects_averages',
            'total_of_stakeholder_identity',
            'percentage_of_stakeholder',
            'comment',
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'surveys_' . time();
    }
}
