<?php
require_once(dirname(__FILE__).'/config.php');

class DiscordWebhooks
{
	protected static $action = '';
	protected static $agent = 'PHP-DISCORD-WEBHOOKS-by-Howar31';

	protected static function cPost (array $params = array())
	{
		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_HEADER         => 0,
			CURLOPT_POST           => true,
			CURLOPT_FRESH_CONNECT  => 1,
			CURLOPT_FORBID_REUSE   => 1,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_USERAGENT      => static::$agent,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_POSTFIELDS     => json_encode($params),
			CURLOPT_URL            => API_URL.static::$action,
			CURLOPT_HTTPHEADER     => array('Content-Type: application/json')

		));
		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}

	protected static function cGet (array $params = array())
	{
		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_HEADER         => 0,
			CURLOPT_POST           => false,
			CURLOPT_FRESH_CONNECT  => 1,
			CURLOPT_FORBID_REUSE   => 1,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_USERAGENT      => static::$agent,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_URL            => API_URL.static::$action.'?'.http_build_query($params),
			CURLOPT_HTTPHEADER     => array('Content-Type: application/json')
		));
		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}

	/**
	 * Execute Webhook
	 * https://discordapp.com/developers/docs/resources/webhook#execute-webhook
	 * @param  array  $input array contain all input parameters:
	 *                           content:    the message contents (up to 2000 characters)
	 *                           username:   override the default username of the webhook
	 *                           avatar_url: override the default avatar of the webhook
	 *                           tts:        true if this is a TTS message
	 *                           file:       the contents of the file being sent
	 *                           embeds:     embedded rich content
	 * @return array         json return:
	 *                           success:    execution success or not
	 *                           error:      error message if failed
	 */
	public function execute (array $input = array())
	{
		$return = array(
			'success' => false,
			'error' => ''
		);

		if (empty($input))
		{
			$return['error'] = 'Input is empty';
			goto END;
		}
		if (empty($input['content']))
		{
			$return['error'] = 'Input content is empty';
			goto END;
		}

		static::$action = 'webhooks/'.WEBHOOK_ID.'/'.WEBHOOK_TOKEN;

		$data = array(
			'content' => trim($input['content']),
			'username' => $input['username'],
			'avatar_url' => $input['avatar_url'],
			'tts' => $input['tts'],
			'file' => $input['file'],
			'embeds' => is_array($input['embeds']) ? $input['embeds'] : array()
		);

		$result = $this->cPost($data);

		if (!IS_LIVE && $result)
		{
			$return['error'] = $result;
			goto END;
		}

	END:
		$return['success'] = empty($return['error']) ? true : false;
		return $return;
	}
}
?>