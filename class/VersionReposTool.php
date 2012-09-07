<?php 
namespace org\opencomb\development\repos ;

use org\opencomb\coresystem\mvc\controller\ControlPanel;
use org\opencomb\platform\ext\Extension;
use org\jecat\framework\bean\BeanFactory;

class VersionReposTool extends Extension 
{
	public function load()
	{
		// 注册菜单build事件的处理函数
		ControlPanel::registerMenuHandler(array(__CLASS__,'buildControlPanelMenu')) ;
	}
	
	static public function buildControlPanelMenu(array & $arrConfig)
	{
		// 合并配置数组，增加菜单
		BeanFactory::mergeConfig(
				$arrConfig
				, BeanFactory::singleton()->findConfig('widget/control-panel-frame-menu','version-repos-tool')
		) ;
	}
}
