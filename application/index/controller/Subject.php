<?php
namespace app\index\controller;

use app\index\model\Subject as SubjectModel;

class Subject extends Common
{
    /**
     * 获取随机答题列表
     *
     * @return \think\response\Json
     */
    public function randList()
    {
        //计算题目总数量total
        $subjectModel = new SubjectModel();
        $subjectList = $subjectModel->select();

        $data = array();
        //全部答题索引ID进行随机获取考试答题ID
        $tids = array();
        foreach($subjectList as $key => $val){
            $data[$val->id]['id'] = $val->id;
            $data[$val->id]['question'] = $val->question;
            $data[$val->id]['a'] = $val->a;
            $data[$val->id]['b'] = $val->b;
            $data[$val->id]['c'] = $val->c;
            $data[$val->id]['d'] = $val->d;
            $data[$val->id]['answer'] = $val->answer;
            $data[$val->id]['dif'] = $val->dif;
            $tids[] = $val->id;
        }

        $totalCount = count($data);
        if($totalCount <= 0){
            return json(array('errorCode' => 1,'msg' => '题库数据异常!'));
        }

        //随机获取答题ID数组
        $newData = array();
        for($i=0;$i<ANSWER_NUM;$i++) {
            $newData[] = $this->getAnswerIds($tids, $newData);
        }

        //随机试题列表
        $answerData = array();
        foreach($newData as $key =>$val){
            if(array_key_exists($val,$data)){
                $answerData[$val] = $data[$val];
            }
        }
        return json(array('errorCode' => 0,'msg' => 'success!','data' => $answerData));
    }


    /**
     * 校验答题是否正确
     * @param tIds = '1|2|3|4|5|'; 题目ID
     * @param sIds = '1|2|3|4|'; 用户选择的题目
     * @return \think\response\Json
     */
    public function check(){
        $tIds = input('tIds') ?input('tIds') : '1|2|3|4|5|1|2|3|4|5|';
        $sIds = input('sIds') ? input('sIds') : 'a|b|c|d|a|x|x|x|x|x|';

        $tIds = trim($tIds,'|');
        $sIds = trim($sIds,'|');
        //查询题目ID对应的答案
        $tData = explode('|',$tIds);
        $sData = explode('|',$sIds);
        $an_len = count($tData); //题目数

        $subjectModel = new SubjectModel();
        $list = $subjectModel->where('id','in',$tData)->select();
        foreach($list as $key => $val){
            $select = '';
            if($val->a == '1'){
                $select = 'a';
            }elseif($val->b == '1'){
                $select = 'b';
            }elseif($val->c == '1'){
                $select = 'c';
            }elseif($val->d == '1'){
                $select = 'd';
            }
            $tmp[$val->id] = $select;
        }

        //正确答案顺序
        foreach($tData as $val){
            if(array_key_exists($val,$tmp)){
                $ssIds[] = $tmp[$val];
            }else{
                $ssIds[] = $val;
            }
        }

        $score = 0; //得分
        $q_right = 0; // 答对题数
        foreach($sData as $key => $val){
            if($ssIds[$key] == $val){
                $q_right += 1;
            }
        }

        $score = round(100/$an_len)*$q_right;
        $result = array(
            'q_right' => $q_right,
            'score' => $score,
            'an_len' => $an_len,
        );
        return json(array('errorCode' => 0,'msg' => 'success!','data' => $result));
    }


    private function getAnswerIds($tids,&$data){
        $rand = rand(0,ANSWER_NUM);
        if(in_array($tids[$rand],$data)){
            return $this->getAnswerIds($tids,$data);
        }else{
            return $tids[$rand];
        }
    }
}
