<?php
namespace Cheyoo\System\Ext\Yii;

interface IStatePersister
{
	/**
	 * Loads state data from a persistent storage.
	 * @return mixed the state
	 */
	public function load();
	/**
	 * Saves state data into a persistent storage.
	 * @param mixed $state the state to be saved
	 */
	public function save($state);
}
