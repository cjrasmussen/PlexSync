<?php
$map = [
	'Movies' => [
		'Movies (Documentary)',
		'Movies (General)',
		'Movies (Holiday)',
		'Movies (Kids)',
	],
	'TV Shows' => [
		'TV Shows (Documentary)',
		'TV Shows (General)',
		'TV Shows (Kids)',
	],
];

$fields = [
	'guid',
	'title',
	'title_sort',
	'original_title',
	'studio',
	'rating',
	'rating_count',
	'tagline',
	'summary',
	'trivia',
	'quotes',
	'content_rating',
	'content_rating_age',
	'duration',
	'user_thumb_url',
	'user_art_url',
	'user_banner_url',
	'user_music_url',
	'user_fields',
	'tags_genre',
	'tags_collection',
	'tags_director',
	'tags_writer',
	'tags_star',
	'year',
	'updated_at',
	'tags_country',
	'extra_data',
	'audience_rating',
];

if (!count($fields)) {
	// MIS-CONFIGURED, JUST BAIL
	exit;
}

$db = new PDO(
	'sqlite:/var/lib/plexmediaserver/Library/Application Support/Plex Media Server/Plug-in Support/Databases/com.plexapp.plugins.library.db',
	null,
	null,
	[
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES => false,
	]
);

$query = '
	SELECT id
	FROM library_sections
	WHERE name = :name
	ORDER BY id ASC
	LIMIT 1';
$stmt_select_library_id = $db->prepare($query);

$select = $where = $set = [];
foreach ($fields AS $field) {
	$select[] = 'meta_source.' . $field;
	$where[] = 'meta_target.' . $field . ' != meta_source.' . $field;
	$set[] = '`' . $field . '` = :' . $field;
}

$query = '
	SELECT ' . implode(',', $select) . ',meta_target.id
	FROM metadata_items meta_source
		INNER JOIN metadata_items meta_target ON ((meta_source.hash = meta_target.hash) AND (meta_source.library_section_id = :source_id) AND (meta_target.library_section_id = :target_id))
	WHERE ((' . implode(') OR (', $where) . '))';
$stmt_select_sync = $db->prepare($query);

$query = '
	UPDATE metadata_items
	SET ' . implode(', ', $set) . '
	WHERE id = :id';
$stmt_update = $db->prepare($query);

foreach ($map AS $source => $targets) {
	$source_library_id = null;

	if (is_numeric($source)) {
		$source_library_id = $source;
	} else {
		$stmt_select_library_id->bindValue('name', $source);
		$stmt_select_library_id->execute();
		$source_library_id = $stmt_select_library_id->fetch()['id'];
	}

	if (!$source_library_id) {
		// COULDN'T GET A SOURCE ID, SKIP THIS ONE
		continue;
	}

	foreach ($targets AS $target) {
		$target_library_id = null;

		if (is_numeric($target)) {
			$target_library_id = $target;
		} else {
			$stmt_select_library_id->bindValue('name', $target);
			$stmt_select_library_id->execute();
			$target_library_id = $stmt_select_library_id->fetch()['id'];
		}

		if (!$target_library_id) {
			// COULDN'T GET A TARGET ID, SKIP THIS ONE
			continue;
		}

		$stmt_select_sync->bindValue('source_id', $source_library_id, PDO::PARAM_INT);
		$stmt_select_sync->bindValue('target_id', $target_library_id, PDO::PARAM_INT);
		$stmt_select_sync->execute();

		while ($record = $stmt_select_sync->fetch()) {
			foreach ($fields AS $field) {
				$stmt_update->bindValue($field, $record[$field]);
			}

			$stmt_update->bindValue('id', $record['id'], PDO::PARAM_INT);
			$stmt_update->execute();
		}
	}
}
