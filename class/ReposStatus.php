<?php
namespace org\opencomb\development\repos ;

use org\opencomb\coresystem\auth\Id;

use org\jecat\framework\fs\imp\LocalFolder;
use org\jecat\framework\fs\FileSystem;
use org\opencomb\platform\ext\ExtensionMetainfo;
use org\opencomb\platform\ext\ExtensionManager;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class ReposStatus extends ControlPanel
{
	public function createBeanConfig()
	{
		return array(
			'title'=>'版本状态',
			'view:statusView' => array(
				'template' => 'ReposStatus.html'
			) ,
			'perms' => array(
				// 权限类型的许可
				'perm.purview'=>array(
					'namespace'=>'coresystem',
					'name' => Id::PLATFORM_ADMIN,
				) ,
			) ,
		) ;
	}
	
	public function process()
	{
		$this->checkPermissions('您没有使用这个功能的权限,无法继续浏览',array()) ;
		
		$arrAllReposStatus['framework'] = array(
				'type' => '框架' ,
				'folder' => FileSystem::singleton()->findFolder('/framework') ,
				'title' => 'JeCat PHP Framework' ,
		) ;
		$this->gitStatus($arrAllReposStatus['framework']) ;
			
		$arrAllReposStatus['platform'] = array(
				'type' => '平台' ,
				'folder' => FileSystem::singleton()->findFolder('/') ,
				'title' => '蜂巢' ,
		) ;
		$this->gitStatus($arrAllReposStatus['platform']) ;
		
		$aExtMgr = ExtensionManager::singleton() ;
		foreach($aExtMgr->metainfoIterator() as $aExtMeta)
		{
			$aExtMeta instanceof ExtensionMetainfo ;
			
			$sExtName = $aExtMeta->name() ;
			$arrAllReposStatus[$sExtName]['type'] = '扩展' ;
			$arrAllReposStatus[$sExtName]['folder'] = FileSystem::singleton()->findFolder($aExtMeta->installPath()) ;
			$arrAllReposStatus[$sExtName]['title'] = $aExtMeta->title() ;
			
			$this->gitStatus($arrAllReposStatus[$sExtName]) ;
		}
		$this->statusView->variables()->set('arrAllReposStatus',$arrAllReposStatus) ;
	}
	
	private function gitStatus(&$arrStatus)
	{
		if( $arrStatus['folder']->findFolder('.git') )
		{
			$arrStatus['repos'] = 'git' ;
			
			if($arrStatus['folder'] instanceof LocalFolder)
			{
				chdir($arrStatus['folder']->url(false)) ;
				$arrStatus['detail'] = `git status -s` ;
				$arrStatus['status'] = empty($arrStatus['detail'])? 'clean': 'modified' ;
				$arrStatus['unpush'] = preg_match("|# Your branch is ahead of '([\\w-_]+?)/([\\w-_]+?)' by (\\d+) commit.|", `git status`,$arrRes)?
						array($arrRes[1].'/'.$arrRes[2] => (int)$arrRes[3]) : array() ;
			}
			else
			{
				$arrStatus['status'] = 'unknow' ;
				$arrStatus['detail'] = '' ;
				$arrStatus['unpush'] = array() ;
			}
		}
		
		else
		{
			$arrStatus['repos'] = 'none' ;
		}
	}
	
}

?>