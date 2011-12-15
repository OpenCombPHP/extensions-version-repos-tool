<?php 
namespace org\opencomb\development\repos ;

use org\jecat\framework\lang\aop\AOP;
use org\opencomb\platform\ext\Extension ;

class VersionReposTool extends Extension 
{
	public function load()
	{
		AOP::singleton()->register('org\\opencomb\\development\\repos\\aspect\\ControlPanelFrameAspect') ;
	}
}