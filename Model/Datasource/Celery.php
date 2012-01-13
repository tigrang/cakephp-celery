<?php
App::uses('AMQP', 'AMQP.Model/Datasource');
class Celery extends AMQP {

    public function publish($routingKey, $task, $args, $extras = array()) {
        $this->connect();
		if (!in_array($routingKey, $this->config['types']) &&
            $this->config['types'] !== array('*')) {
			return false;
		}

        $messageId = uniqid('Task_');
        $message = array(
            'id' => $messageId,
            'task' => $task,
            'args' => array(json_encode($args)),
            'kwargs' => (object)array(),
        );
        $message = json_encode(array_merge($message, $extras));

        $params = array('Content-type' => 'application/json',
            'Content-encoding' => 'UTF-8',
            'immediate' => false,
        );

        $result = $this->_exchange->publish($message, $routingKey, 0, $params);
        $this->disconnect();

        if ($result) {
            return $messageId;
        }

        return false;
    }

}