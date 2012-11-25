<?php
App::uses('AMQP', 'AMQP.Model/Datasource');
class Celery extends AMQP {

	public function publish($message, $routingKey, $flags = AMQP_NOPARAM, $attributes = []) {
		$messageId = uniqid('Task_');

		$task = $attributes['task'];
		unset($attributes['task']);

		$message = [
			'id' => $messageId,
			'task' => $task,
			'args' => [json_encode($message)],
			'kwargs' => (object)[],
		];

		$attributes = array_merge(['content_type' => 'application/json', 'content_encoding' => 'UTF-8'], $attributes);

		if (parent::publish($message, $routingKey, $flags, $attributes)) {
			return $messageId;
		}

		return false;
	}

}