<?php
/**
 * 多进程服务
 * 主要使用 pcntl_fork  
 */
class Daemons{

	protected $runPath = '/tmp/';


	public  function startWith($str, $prefix) {
        if (strlen($prefix) > strlen($str)) {
            return false;
        }

        return substr($str, 0, strlen($prefix)) === $prefix;
    }
	 /**
     * 多进程常驻进程控制器
     *
     * @param     $daemonName string 服务名称
     * @param     $callback callback 回调函数, 会传入两个int参数,表示(一共几个进程, 这是第几个进程); 返回值bool,表示是否连续取下一个值(不sleep)
     * @param int $processAmount 启动并发进程个数
     */
    protected function restartDaemons($daemonName, $callback, $processAmount = 1)
    {
        $daemonName = strtolower($daemonName);
        //通过pid文件来控制子进程存活
        $pidDir = $this->runPath . DIRECTORY_SEPARATOR . 'pid';
        if (!is_dir($pidDir)) {
            mkdir($pidDir, 0777, true);
        }
        //清除之前的所有子进程, 这些子进程就会安全退出
        $pidDirHandle = opendir($pidDir);
        while ($fileName = readdir($pidDirHandle)) {
            if ($this->startWith($fileName, $daemonName . '.worker.')) {
                @unlink($pidDir . DIRECTORY_SEPARATOR . $fileName);
                echo 'CLEAN_PID_FILE' . $fileName . PHP_EOL;
            }
        }

        //记录主进程pid
        for ($i = 0; $i < $processAmount; $i++) {
            $pid = pcntl_fork();
            if ($pid == 0) {
                //子进程处理逻辑
                $pid = getmypid();
                $pidFile = $pidDir . DIRECTORY_SEPARATOR . $daemonName . '.worker.' . $pid;

                touch($pidFile);
                echo "worker start: " . ($i + 1) . "/$processAmount" . PHP_EOL;

                while (file_exists($pidFile)) {
                    $roundStartTime = microtime(true);
                    $runSuccess = $callback($processAmount, $i);
                    if (!$runSuccess) {
                        sleep(1);
                    } else {
                        echo 'DAEMON_ROUND_TIME' . microtime(true) . '-' . $roundStartTime . ( microtime(true) - $roundStartTime ) . '-' . $daemonName . '-' . $i . '-' . $processAmount . PHP_EOL;
                    }
                }
                echo "worker exit: " . ($i + 1) . "/$processAmount" . PHP_EOL;
                exit(0);
            }
        }
        //主进程等待子进程退出
        for ($i = 0; $i < $processAmount; $i++) {
            $spId = pcntl_wait($status);
            if ($status != 0) {
            	echo 'DAEMON_WORKER_FAILED' . '-' . $daemonName .'-' . $spId .'-' .$status . PHP_EOL;
            }
        }
    }
    


    /**
     * 多进程分发器
     *
     * @param int $processAmount 启动并发进程个数
     * @param     $callback callback 回调函数, 会传入两个int参数,表示(一共几个进程, 这是第几个进程)
     */
    public function startMultiProcess($processAmount, $callback)
    {
        

        //记录主进程pid
        for ($i = 0; $i < $processAmount; $i++) {
            $pid = pcntl_fork();
            if ($pid == 0) {
                //子进程处理逻辑
                $pid = getmypid();

                echo "worker start: " . ($i + 1) . "/$processAmount" . PHP_EOL;

                $roundStartTime = microtime(true);
                $runSuccess = $callback($processAmount, $i);
                if (!$runSuccess) {
                    sleep(1);
                } else {
                    echo 'DAEMON_ROUND_TIME' . microtime(true) . '-' . $roundStartTime . ( microtime(true) - $roundStartTime ) . '-' . $daemonName . '-' . $i . '-' . $processAmount . PHP_EOL;
                }
                echo "worker exit: " . ($i + 1) . "/$processAmount" . PHP_EOL;
                exit(0);
            }
        }
        //主进程等待子进程退出
        for ($i = 0; $i < $processAmount; $i++) {
            $spId = pcntl_wait($status);
            if ($status != 0) {
            	echo 'DAEMON_WORKER_FAILED' . '-' . $daemonName .'-' . $spId .'-' .$status . PHP_EOL;
            }
        }
    }

    public function test(){
    	$this->startMultiProcess(10,function(){
    		echo 1;
    	});
    }
}

$model = new Daemons();
$model->test();
