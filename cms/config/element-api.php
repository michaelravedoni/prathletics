<?php

use craft\elements\Entry;
use craft\helpers\UrlHelper;

return [
    'endpoints' => [
        'sessions.json' => function() {
			\Craft::$app->response->headers->set('Access-Control-Allow-Origin', '*');
            return [
                'elementType' => Entry::class,
                'criteria' => ['section' => 'sessions'],
				'transformer' => function(Entry $entry) {
					return [
						'title' => $entry->title,
						'id' => $entry->id,
						'url' => $entry->url,
						'slug' => $entry->slug,
						'type' => $entry->type['handle'],
						'schedule' => $entry->schedule,
					];
				},
				'pretty' => true,
            ];
        },
		'sessions/<entryId:\d+>.json' => function($entryId) {
			\Craft::$app->response->headers->set('Access-Control-Allow-Origin', '*');
            return [
                'elementType' => Entry::class,
                'criteria' => ['id' => $entryId],
				'one' => true,
				'transformer' => function(Entry $entry) {
					$relatedGroup = \craft\elements\Entry::find()->relatedTo($entry)->section('groups')->one();
					$ancestorsA = $entry->getAncestors()->all();
					$ancestors = [];
					foreach ($ancestorsA as $a) {
						$relatedSeason = \craft\elements\Category::find()->relatedTo($a)->one();
						$ancestors[] = [
							'title' => $a->title,
							'id' => $a->id,
							'url' => $a->url,
							'uri' => $a->uri,
							'slug' => $a->slug,
							'typeCadre' => $a->typeCadre,
							'typeCadreLabel' => [
								'title' => $relatedSeason['title'],
								'slug' => $relatedSeason['slug'],
								'titleLong' => $relatedSeason['titleLong']
							],
							'cadreLabel' => $a->label,
						];
					}
					$childrensA = $entry->getChildren()->all();
					$childrens = [];
					foreach ($childrensA as $c) {
						$childrens[] = [
							'title' => $c->title,
							'id' => $c->id,
							'url' => $c->url,
							'uri' => $c->uri,
							'slug' => $c->slug,
							'typeCadre' => $c->typeCadre,
						];
					}
					$blockMatrix = [];
					if ($entry->block) {
						foreach ($entry->getFieldValue('block')->all() as $block) {
							switch ($block->type->handle) {
								case 'free':
								$informations = $block->informations->first();
								$blockMatrix[] = [
									'heading' => $informations->heading,
								'note' => $informations->note,
								'time' => $informations->time,
								'rest' => $informations->rest,
								'text' => $block->text,
								];
								break;
								case 'block':
								$informations = $block->informations->first();
								$exercicesA = $block->exercices->all();
								$exercices = [];
								foreach ($exercicesA as $ex) {
									$relatedE = \craft\elements\Entry::find()->relatedTo($ex)->one();
									$relatedEProps = $relatedEProps = [
										'title' => $relatedE['title'],
									'url' => $relatedE['url'],
									'uri' => $relatedE['uri'],
									'slug' => $relatedE['slug'],
									'id' => $relatedE['id'],
									'exerciceDescription' => $relatedE['exerciceDescription'],
									'note' => $relatedE['note'],
									'difficulty' => $relatedE['difficulty'],
									'exerciceIntensite' => $relatedE['exerciceIntensite'],
									'repetitionsMinimum' => $relatedE['repetitionsMinimum'],
									'repetitionsMaximum' => $relatedE['repetitionsMaximum'],
									];
									$exercices[] = [
										'exercice' => $ex->exercice,
									'series' => $ex->series,
									'repetitions' => $ex->repetitions,
									'rest' => $ex->rest,
									'note' => $ex->note,
									'exerciceRelation' => !empty($relatedE) ? $relatedEProps : null
										];
								}
								$blockMatrix[] = [
									'heading' => $informations->heading,
								'note' => $informations->note,
								'time' => $informations->time,
								'rest' => $informations->rest,
								'exercices' => $exercices
									];
								break;
							}
						}
					}
					return [
						'title' => $entry->title,
						'id' => $entry->id,
						'url' => $entry->url,
						'slug' => $entry->slug,
						'type' => $entry->type['handle'],
						'group' => $relatedGroup,
						'schedule' => $entry->schedule,
						'blocks'  => $entry->block ? $blockMatrix : null,
						'ancestors' => $ancestors,
						'childrens' => $childrens,
					];
				},
				'pretty' => true,
            ];
        },
		'groups.json' => function() {
			\Craft::$app->response->headers->set('Access-Control-Allow-Origin', '*');
            return [
                'elementType' => Entry::class,
                'criteria' => ['section' => 'groups'],
				'transformer' => function(Entry $entry) {
					$relatedSessionsA = \craft\elements\Entry::find()->relatedTo($entry)->orderBy('schedule asc')->limit(5)->all();
					$relatedSessions = [];
					foreach ($relatedSessionsA as $session) {
						$relatedSessions[] = [
							'title' => $session->title,
							'id' => $session->id,
							'url' => $session->url,
							'uri' => $session->uri,
							'slug' => $session->slug,
							'schedule' => $session->schedule,
						];
					}
					$next = \craft\elements\Entry::find()->section('sessions')->relatedTo($entry)->orderBy('schedule asc')->schedule('>= '. date(DATE_ATOM))->one();
					$scheduleString = new DateTime($next['schedule']->format(DATE_ATOM));
					return [
						'title' => $entry->title,
						'id' => $entry->id,
						'url' => $entry->url,
						'slug' => $entry->slug,
						'groupDescription' => $entry->groupDescription,
						'sessions' => $relatedSessions,
						'nextSession' => [
							'title' => $next['title'],
							'id' => $next['id'],
							'uri' => $next['uri'],
							'slug' => $next['slug'],
							'schedule' => $next['schedule'],
							'fromNow' => date_diff($scheduleString,new DateTime())->format('P%yY%mM%dDT%hH%mM%sS'),
							'fromNowString' => $scheduleString->diff(new DateTime())->format("%d jours %hh et %im"),
						],
					];
				},
				'pretty' => true,
            ];
        },
		'groups/<entryId:\d+>.json' => function($entryId) {
			\Craft::$app->response->headers->set('Access-Control-Allow-Origin', '*');
            return [
                'elementType' => Entry::class,
                'criteria' => ['id' => $entryId],
				'one' => true,
				'transformer' => function(Entry $entry) {
					$relatedSessionsA = \craft\elements\Entry::find()->relatedTo($entry)->orderBy('schedule desc')->limit(9)->all();
					$relatedSessions = [];
					foreach ($relatedSessionsA as $session) {
						$relatedSessions[] = [
							'title' => $session->title,
							'id' => $session->id,
							'url' => $session->url,
							'slug' => $session->slug,
							'schedule' => $session->schedule,
						];
					}
					$next = \craft\elements\Entry::find()->section('sessions')->relatedTo($entry)->orderBy('schedule desc')->schedule('>= '. date(DATE_ATOM))->one();
					return [
						'title' => $entry->title,
						'id' => $entry->id,
						'url' => $entry->url,
						'slug' => $entry->slug,
						'groupDescription' => $entry->groupDescription,
						'sessions' => $relatedSessions,
						'nextSession' => [
							'title' => $next['title'],
							'id' => $next['id'],
							'uri' => $next['uri'],
							'slug' => $next['slug'],
							'schedule' => $next['schedule'],
							'uri' => $next['uri'],
							'uri' => $next['uri'],
						],
					];
				},
				'pretty' => true,
            ];
        },
		'drills.json' => function() {
			\Craft::$app->response->headers->set('Access-Control-Allow-Origin', '*');
            return [
                'elementType' => Entry::class,
                'criteria' => ['section' => 'drills'],
				'transformer' => function(Entry $entry) {
					$categoryA = \craft\elements\Category::find()->relatedTo($entry)->all();
					$category = [];
					foreach ($categoryA as $cat) {
						$category = [
							'id' => $cat->id,
							'title' => $cat->title,
							'slug' => $cat->slug,
						];
					}
					return [
						'title' => $entry->title,
						'id' => $entry->id,
						'url' => $entry->url,
						'slug' => $entry->slug,
						'category' => $category,
					];
				},
				'pretty' => true,
            ];
        },
		'drills/<entryId:\d+>.json' => function($entryId) {
			\Craft::$app->response->headers->set('Access-Control-Allow-Origin', '*');
            return [
                'elementType' => Entry::class,
                'criteria' => ['id' => $entryId],
				'one' => true,
				'transformer' => function(Entry $entry) {
					$categoryA = \craft\elements\Category::find()->relatedTo($entry)->all();
					$category = [];
					foreach ($categoryA as $cat) {
						$category = [
							'id' => $cat->id,
							'title' => $cat->title,
							'slug' => $cat->slug,
						];
					}
					$blockMatrix = [];
					foreach ($entry->getFieldValue('block')->all() as $block) {
						switch ($block->type->handle) {
							case 'free':
							$informations = $block->informations->first();
							$blockMatrix[] = [
								'heading' => $informations->heading,
								'note' => $informations->note,
								'time' => $informations->time,
								'rest' => $informations->rest,
								'text' => $block->text,
							];
							break;
							case 'block':
							$informations = $block->informations->first();
							$exercicesA = $block->exercices->all();
							$exercices = [];
							foreach ($exercicesA as $ex) {
								$relatedE = \craft\elements\Entry::find()->relatedTo($ex)->one();
								$relatedEProps = $relatedEProps = [
									'title' => $relatedE['title'],
									'id' => $relatedE['id'],
									'url' => $relatedE['url'],
									'uri' => $relatedE['uri'],
									'slug' => $relatedE['slug'],
									'exerciceDescription' => $relatedE['exerciceDescription'],
									'note' => $relatedE['note'],
									'difficulty' => $relatedE['difficulty'],
									'exerciceIntensite' => $relatedE['exerciceIntensite'],
									'repetitionsMinimum' => $relatedE['repetitionsMinimum'],
									'repetitionsMaximum' => $relatedE['repetitionsMaximum'],
								];
								$duration = ($ex->series ? $ex->series.'x': null).($ex->repetitions ? $ex->repetitions: null).($ex->series == null && $ex->repetitions ? 'x': null).($ex->rest ? ' ['.$ex->rest.']': null);
								$exercices[] = [
									'exercice' => $ex->exercice,
									'series' => $ex->series,
									'repetitions' => $ex->repetitions,
									'rest' => $ex->rest,
									'duration' => $duration,
									'note' => $ex->note,
									'exerciceRelation' => !empty($relatedE) ? $relatedEProps : null
								];
								}
							$blockMatrix[] = [
								'heading' => $informations->heading,
								'note' => $informations->note,
								'time' => $informations->time,
								'rest' => $informations->rest,
								'exercices' => $exercices
								];
							break;
						}
					}
					return [
						'title' => $entry->title,
						'id' => $entry->id,
						'url' => $entry->url,
						'slug' => $entry->slug,
						'description' => $entry->description,
						'schedule' => $entry->schedule,
						'category' => $category,
						'blocks'  => $blockMatrix,
					];
				},
				'pretty' => true,
            ];
        },
		'exercices.json' => function() {
			\Craft::$app->response->headers->set('Access-Control-Allow-Origin', '*');
            return [
                'elementType' => Entry::class,
                'criteria' => ['section' => 'exercices'],
				'transformer' => function(Entry $entry) {
					return [
						'title' => $entry->title,
						'id' => $entry->id,
						'url' => $entry->url,
						'slug' => $entry->slug,
					];
				},
				'pretty' => true,
            ];
        },
		'exercices/<entryId:\d+>.json' => function($entryId) {
			\Craft::$app->response->headers->set('Access-Control-Allow-Origin', '*');
            return [
                'elementType' => Entry::class,
                'criteria' => ['id' => $entryId],
				'one' => true,
				'transformer' => function(Entry $entry) {
					$illustration = \craft\elements\Asset::find()->relatedTo($entry)->one();
					$type = \craft\elements\Tag::find()->relatedTo($entry)->one();
					return [
						'title' => $entry->title,
						'id' => $entry->id,
						'url' => $entry->url,
						'slug' => $entry->slug,
						'exerciceDescription' => $entry->exerciceDescription,
						'note' => $entry->note,
						'exerciceType' => $type,
						'difficulty' => $entry->difficulty,
						'exerciceIntensite' => $entry->exerciceIntensite,
						'repetitionsMinimum' => $entry->repetitionsMinimum,
						'repetitionsMaximum' => $entry->repetitionsMaximum,
						'illustration' => $illustration,
					];
				},
				'pretty' => true,
            ];
        },
		
    ]
];