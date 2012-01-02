<?php
App::uses('AMQP', 'AMQP.Model/Datasource');
class Celery extends AMQP {

    public function publish($type, $task, $args, $extras=array()) {
        $this->connect();
		if (!in_array($type, $this->config['types']) &&
            $this->config['types'] !== array('*')) {
			return false;
		}

        $message = array(
            'id' => uniqid('Task_'),
            'task' => $task,
            'args' => $args,
            'kwargs' => (object)array(),
        );
        $message = json_encode(array_merge($message, $extras));

        $params = array('Content-type' => 'application/json',
            'Content-encoding' => 'UTF-8',
            'immediate' => false,
        );

		$result = $this->_exchange->publish($message, $type, 0, $params);
        $this->disconnect();

        return $result;
    }

}