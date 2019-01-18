<?php 
namespace app\user\Controller;
use think\Controller;
use think\Db;
use app\user\model\User as UserModel;
use think\Loader;
use think\cache\driver\Redis;

//用户端管理代码(暂未加密传输密码)

class User extends Controller
{
    /**  
    * 用户信息注册
    * 
    * @access public 
    * @param string $data 前端post传来的全部数据信息
    * @param array $array 需要插入数据库参数的数组 
    * @return 返回正确或错误标号
    */  
	public function register()
    {
		if(request()->isPost()){
            $data=input('post.');	
		    //用户名不能为空且密码不少于6位
	        if(empty($data['username'])&&strlen($data['password'])<=6)
            {
                return json_encode('x');
            }
	        //其他注册失败条件
	        if(aa)
            {
                return x;
            }	
        $array = ['foo' => 'bar', 'bar' => 'foo'];
        $sql=Db::table('db_name')->insert($array);
        if($sql)
        {
            return json_encode('x');
        }
    }
}

    /**  
    *  用户登录
    * 
    * @access public 
    * @param string $user model中定义的用户登录条件
    * @return 返回正确或错误标号
    */  
	public function landing()
    {
		if(request()->isPost()){
            $data=input('post.');
			$user = new UserModel;
            if ($user->login($data) == 1) 
            {
                //用户名密码正确，成功登陆
                echo json_encode('x');
            }
            elseif($user->login($data) == 2) 
            {
                //用户名或密码错误
                return json_encode('x');
            }
            elseif($user->login($data) == 3)
            {
                //用户名不存在
                return json_encode('x');
            }
        }
    }

    /**  
    *  用户修改密码或用户名
    * 
    * @access public 
    * @param string $username   用户原用户名
    * @param string $username1  用户更改后的用户名 
    * @param string $password   用户原密码
    * @param string $password1  用户修改后的密码
    * @param string $pas        获取的数据库中原密码
    * @return 返回正确或错误标号
    */  
	public function change()
	{
        if(request()->isPost())
        {
            //修改用户名
            $username1=input('username1');
            $username=input('username');
            $sql=Db::table('db_name')->where('username',$username)->update(['username'=>$username1]);
          
            if($sql){
            return json_encode('x');
           }
            //修改密码
            $pas=Db::table('db_name')->where('username',$username)->select();
            $password=input('password');
            $password1=input('password1');
            //数据库中密码与输入密码相等，修改密码
            if($pas['password'] == $password)
            {
                $sql==Db::table('db_name')->where('username',$username)->update(['password'=>$password]);
                if($sql){
                    return json_encode('x');
                }
             }
            else{
                return json_encode('x');
            }
        }
	}

    /**  
    * 获取手机验证码(暂用的阿里云短信服务实验） 
    * 
    * @access public 
    * @return 返回正确或错误标号
    */  
	public function getmsg()
	{
		    if(request()->isPost()){          
            $number = input('number'); 
            Loader::import('alimsg.api_demo.SmsDemo',EXTEND_PATH);
            $code =$this->random();
            //得到信息文件并执行.实例化阿里短信类
            $msg = new \SmsDemo('Access key id','Access key secret');
            $res = $msg->sendSms(
                //短信签名名称
                "学习试验",
                //短信模板code
                "SMS_142075303",
                //短信接收者的手机号码
               "$number",
                //模板信息
                Array(
                    'code' => $code,
                )
            );
            //对象转数组
            $response = (array)$res;
            var_dump($response["Message"]);
            //发送信息成功
            if($response["Message"]=='OK')
            {
                $redis = new Redis();
                $redis->set('user:phone:'.$number.':code',$code,3000);
            }
        }
    }

    /**  
    *生成所发送的验证码并返回(生成随机数)
    * 
    * @access public 
    * @param string $length 随机数长度
    * @return string $code 随机数
    */  
    public function random()
    {
        $length = 6;
        $char = '0123456789';
        $code = '';
        while(strlen($code) < $length){
            //截取字符串长度
            $code .= substr($char,(mt_rand()%strlen($char)),1);
        }
        return $code;
    }

    /**  
    *  验证验证码是否正确
    * 
    * @access public 
    * @param string $code 用户输入的验证码
    * @param string $code1 redis缓存的验证码
    * @return X  返回状态
    */  
    public function check()
    {
        $data=input('post.');
        {
            $code=$data['number'];
            $redis = new Redis();
            $code1=$redis->get('user:phone:'.$code.':code');
            if($data['code']==$code1)
            {
                return json_encode('x');
            }
            else
                return json_encode('x');
        }
    }
}

 ?>