<?php
namespace app\Controller;

use app\BaseController;
use think\facade\Db;

class Demo extends BaseController {

    public function index(){
        //获取方法名
        $action = $this->request->action();
        echo $action;
        //获取Base的路径
        $path = $this->app->getBasePath();
        echo $path;
    }



    //更新数据
    public function update(){
        //save 传递主键
        echo Db::name('user')->save(['id' => 10, 'name' => 'thinkphp']);
        //update
        echo Db::name('user')->where('id', 11)->update(['name' => 'thinkphp2']);
        //支持data传值,update的值优先
        echo Db::name('user')->where('id', 12)->data(['name' => 'thinkphp15','tel'=>999])->update(['name' => 'thinkphp12']);
        echo Db::name('user')->where('id', 13)->data(['id' => 13,'name' => 'thinkphp13'])->save();
        //update的参数中有主键，可以直接使用，不用where
        echo Db::name('user')->update(['name' => 'thinkphp15','id' => 15]);

        //支持使用raw方法进行数据更新。
        Db::name('user')
            ->where('id', 15)
            ->update([
                'name'		=>	Db::raw('UPPER(name)'),
                'tel'		=>	Db::raw('tel-3'),
            ]);
        echo Db::getLastSql();

        //自增自减,同时存在后面起作用
        Db::name('user')
            ->where('id', 1)
            ->inc('tel', 5)//自增5
            ->dec('tel')//自减1
            ->update();
        dump(Db::getLastSql());


        //exp函数
        echo Db::name('user')->where(['id'=>13])->exp('name','UPPER(name)')->save();

    }

    //新增数据
    public function add(){



        //save，自动判断是新增还是更新数据（以写入数据中是否存在主键数据为依据）
//        $data = ['name' => 'bar', 'tel' => '123','id'=>3];
        //字段不存在会抛异常
//        echo Db::name('user')->save($data);
        //strict方法用于设置是否严格检查字段名
        //'fields_strict'  => false,//数据库配置
//        echo Db::name('user')->strict(false)->save($data);

        //insert
//        $data = ['name' => 'bar222', 'tel' => '123123123'];
//        $data2 = [
//            ['name' => '黧黑', 'tel' => '111222'],
//            ['name' => '李白', 'tel' => '222111']
//        ];
        //批量插入,必须要是二维数组，
//        echo Db::name('user')->insertAll($data);
        //单条插入
//        echo Db::name('user')->insert($data);

        //如果是mysql数据库，支持replace写入（有住建会变成修改操作），例如：
        $data = ['name' => 'bar', 'tel' => '111'];
        echo Db::name('user')->replace()->insert($data);
        //insertGetId获取新增id
//        echo Db::name('user')->insertGetId($data);



    }

    //分批查询,可以节省内存开销
    public function batch(){
        $cursor = Db::name('user')->where('id', '<>',3)->cursor();
        dump($cursor);die;
        foreach($cursor as $user){
            echo Db::getLastSql();
            echo $user['name'];
        }
        //如果你需要处理成千上百条数据库记录，可以考虑使用chunk方法，该方法一次获取结果集的一小块，
        //然后填充每一小块数据到要处理的闭包，该方法在编写处理大量数据库记录的时候非常有用。
        //比如，我们可以全部用户表数据进行分批处理，每次处理 100 个用户记录：
//        Db::table('tp_user')
//            ->where('id','>',0)
//            ->chunk(100, function($users) {
//                foreach ($users as $user) {
//                    dump($user);
//                    //return false;可以终止查询
//                }
//            },'id', 'desc');
    }

    //值和列查询
    public function valueOrColumn(){
        // 返回某个字段的值,不存在返回null
        $user = Db::table('tp_user')->where('id', 3)->value('name');
        var_dump($user);
        //返回一列的值，索引数组
        $user2 = Db::table('tp_user')->column('name');
        //返回一列的值，tel为key的关联数组
        $user2 = Db::table('tp_user')->column('name','tel');
        //不存在返回空数组
        $user2 = Db::table('tp_user')->where('id',3)->column('name','tel');
        //返回指定数据
        $user2 = Db::table('tp_user')->column('*','tel');
        $user2 = Db::table('tp_user')->column('name,tel','tel');
        dump($user2);
    }


    //一条数据，返回数组
    public function find(){
        //findOrEmpty
        $user = Db::name('user')->where('id',2)->findOrEmpty();//[]
        //findOrFail
        $user = Db::name('user')->where('id',2)->find();//null
        $user = Db::name('user')->where('id',2)->findOrFail();//抛出异常错误
        return json($user);
    }
    //多条数据的查询，返回Collection结果集对象
    public function select(){
        $users = Db::name("user")->select();
        var_dump($users);
        //toArray()转换成数组
        $users = $users->toArray();
        var_dump($users);
        //selectOrFail和findOrFail一样
        $user = Db::name('user')->where('id',3)->selectOrFail();//抛出异常错误
        var_dump($user);
    }

}