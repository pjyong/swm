<?php
namespace Cheyoo\System\Ext\Yii;

interface IApplicationComponent
{
	/**
	 * Initializes the application component.
	 * This method is invoked after the application completes configuration.
	 */
	public function init();
	/**
	 * @return boolean whether the {@link init()} method has been invoked.
	 */
	public function getIsInitialized();
}
