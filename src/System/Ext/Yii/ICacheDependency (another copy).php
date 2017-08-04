<?php
namespace Cheyoo\System\Ext\Yii;

interface ICacheDependency
{
	/**
	 * Evaluates the dependency by generating and saving the data related with dependency.
	 * This method is invoked by cache before writing data into it.
	 */
	public function evaluateDependency();
	/**
	 * @return boolean whether the dependency has changed.
	 */
	public function getHasChanged();
}
