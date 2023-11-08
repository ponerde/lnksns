<?php
namespace app\admin\command;

use app\admin\model\auth\AdminModel;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;


class LiteCommand extends Command
{
    protected function configure()
    {
        $this->setName('lite')
        	->addArgument('name', Argument::OPTIONAL, "your command")
        	->setDescription('LiteAdmin Command');
    }

    protected function execute(Input $input, Output $output)
    {
    	$name = trim($input->getArgument('name'));
      	if($name == 'resPassword'){
            
            $admin = AdminModel::where('username','admin')->find();
             // 每次修改密码，都重新生成 salt
             $salt = mt_rand(1000, 9999);
             $admin->salt = $salt;
             $admin->password = (new AdminModel())->encryptPassword('123456', $salt);
             $admin->save();
        }

		
        $output->writeln("Success");
    }
}