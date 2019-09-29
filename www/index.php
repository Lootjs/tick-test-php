<?php 

require_once __DIR__ . '/vendor/autoload.php';

$events = [];
$events_file = 'events.json';
$event_list = file_exists($events_file) ? json_decode(file_get_contents($events_file)) : [];

foreach ($event_list as $event) {
	$events[$event->id] = $event;
}

$klein = new \Klein\Klein();

$klein->respond('GET', '/', function() {
	return 'go to /events';
});

$klein->respond('GET', '/events', function($request, $response) use ($events) {
	return $response->json($events);
});

$klein->respond('POST', '/event/[i:id]', function($request, $response) use ($events, $events_file) {
	if (empty($events[$request->id])) {
		return '404 error';
	}

	$params = json_decode($request->body());
	$events[$request->id] = [
		'id' => $request->id,
		'event_name' => $params->event_name,
		'event_image' => $params->event_image,
		'event_info' => $params->event_info,
	];

	file_put_contents($events_file, json_encode($events));

	return $response->json($events[$request->id]);
});

$klein->dispatch();
