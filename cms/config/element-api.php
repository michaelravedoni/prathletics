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
					$relatedGroupsA = \craft\elements\Entry::find()->relatedTo($entry)->section('groups')->all();
					$groups = [];
					$athletes = [];
					foreach ($relatedGroupsA as $g) {
						$relatedAthletesA = \craft\elements\Entry::find()->relatedTo($g)->section('athletes')->all();
						$athletes = [];
						foreach ($relatedAthletesA as $a) {
							$athletes[] = [
								'title' => $a->title,
								'id' => $a->id,
								'url' => $a->url,
								'uri' => $a->uri,
								'slug' => $a->slug,
							];
						}
						$groups[] = [
							'title' => $g->title,
							'id' => $g->id,
							'url' => $g->url,
							'uri' => $g->uri,
							'slug' => $g->slug,
							'athletes' => $athletes,
						];
					}
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
					$planA = $entry->sessionPlan->first();
					$plan = [
						'title' => $planA->title,
						'longTitle' => $planA->titleLong,
						'id' => $planA->id,
						'url' => $planA->url,
						'uri' => $planA->uri,
						'slug' => $planA->slug,
					];
					$sessionDate = new DateTime($entry->schedule->format('c'));
					$week = (int)$sessionDate->format("W");
					$planPeriods = $planA->plan;
					foreach ($planPeriods as $p) {
						$periodA = $p->period->first();
						if ((int)$week >= (int)$p->weekStart && (int)$week <= (int)$p->weekEnd) {
							$period = [
								'title' => $periodA->title,
								'longTitle' => $periodA->titleLong,
								'label' => $p->periodLabel,
								'id' => $periodA->id,
								'url' => $periodA->url,
								'uri' => $periodA->uri,
								'slug' => $periodA->slug,
							];
						}
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
								$exercisesA = $block->exercises->all();
								$exercises = [];
								foreach ($exercisesA as $ex) {
									$relatedE = \craft\elements\Entry::find()->relatedTo($ex)->one();
									$illustration = \craft\elements\Asset::find()->relatedTo($relatedE)->one();
									$relatedEProps = $relatedEProps = [
										'title' => $relatedE['title'],
										'id' => $relatedE['id'],
										'url' => $relatedE['url'],
										'uri' => $relatedE['uri'],
										'slug' => $relatedE['slug'],
										'exerciseDescription' => $relatedE['exerciseDescription'],
										'note' => $relatedE['note'],
										'difficulty' => $relatedE['difficulty'],
										'exerciseIntensity' => $relatedE['exerciseIntensity'],
										'illustration' => $illustration,
									];
									$duration = ($ex->series ? $ex->series.'x': null).($ex->repetitions ? $ex->repetitions: null).($ex->series == null && $ex->repetitions ? 'x': null).($ex->rest ? ' ['.$ex->rest.']': null);
									$exercises[] = [
										'exercise' => $ex->exercise,
										'series' => $ex->series,
										'repetitions' => $ex->repetitions,
										'rest' => $ex->rest,
										'duration' => $duration,
										'note' => $ex->note,
										'exerciseRelation' => !empty($relatedE) ? $relatedEProps : null
									];
								}
								$blockMatrix[] = [
									'heading' => $informations->heading,
									'note' => $informations->note,
									'time' => $informations->time,
									'rest' => $informations->rest,
									'exercises' => $exercises
								];
								break;
								case 'athletes':
								$informations = $block->informations->first();
								$programsA = $block->programs->all();
								$programs = [];
								foreach ($programsA as $p) {
									$athletesA = $p->athletes->all();
									$athletesP = [];
									foreach ($athletesA as $a) {
										$athletesP[] = [
											'title' => $a['title'],
											'id' => $a['id'],
											'url' => $a['url'],
											'uri' => $a['uri'],
											'slug' => $a['slug'],
										];
									}
									$program = $p->program->first();
									$programs[] = [
										'title' => $program['title'],
										'id' => $program['id'],
										'url' => $program['url'],
										'uri' => $program['uri'],
										'slug' => $program['slug'],
										'athletes' => $athletesP,
									];
								}
								$blockMatrix[] = [
									'heading' => "Programmes personnalisés",
									'text' => $informations->text,
									'time' => $informations->time,
									'rest' => $informations->rest,
									'programs' => $programs,
								];
								break;
								case 'blockType':
								$blockType = $block->blockType->first();
								foreach ($blockType->getFieldValue('block')->all() as $b) {
									$informations = $b->informations->first();
									$exercisesA = $b->exercises->all();
									$exercises = [];
									foreach ($exercisesA as $ex) {
										$relatedE = \craft\elements\Entry::find()->relatedTo($ex)->one();
										$illustration = \craft\elements\Asset::find()->relatedTo($relatedE)->one();
										$relatedEProps = $relatedEProps = [
											'title' => $relatedE['title'],
											'id' => $relatedE['id'],
											'url' => $relatedE['url'],
											'uri' => $relatedE['uri'],
											'slug' => $relatedE['slug'],
											'exerciseDescription' => $relatedE['exerciseDescription'],
											'note' => $relatedE['note'],
											'difficulty' => $relatedE['difficulty'],
											'exerciseIntensity' => $relatedE['exerciseIntensity'],
											'illustration' => $illustration,
										];
										$duration = ($ex->series ? $ex->series.'x': null).($ex->repetitions ? $ex->repetitions: null).($ex->series == null && $ex->repetitions ? 'x': null).($ex->rest ? ' ['.$ex->rest.']': null);
										$exercises[] = [
											'exercise' => $ex->exercise,
											'series' => $ex->series,
											'repetitions' => $ex->repetitions,
											'rest' => $ex->rest,
											'duration' => $duration,
											'note' => $ex->note,
											'exerciseRelation' => !empty($relatedE) ? $relatedEProps : null
										];
									}
									$blockMatrix[] = [
										'heading' => $informations->heading,
										'note' => $informations->note,
										'time' => $informations->time,
										'rest' => $informations->rest,
										'exercises' => $exercises
									];
								}
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
						'groups' => $groups,
						'athletes' => $athletes,
						'schedule' => $entry->schedule,
						'blocks'  => $entry->block ? $blockMatrix : null,
						'ancestors' => $ancestors,
						'childrens' => $childrens,
						'plan' => $plan,
						'period' => $period,
						'week' => $week,
						'label' => $entry->sessionLabel,
					];
				},
				'pretty' => true,
			];
		},
		'athletes.json' => function() {
			\Craft::$app->response->headers->set('Access-Control-Allow-Origin', '*');
			return [
				'elementType' => Entry::class,
				'criteria' => ['section' => 'athletes'],
				'transformer' => function(Entry $entry) {
					$group = \craft\elements\Entry::find()->section('groups')->relatedTo($entry)->one();
					return [
						'title' => $entry->title,
						'id' => $entry->id,
						'url' => $entry->url,
						'slug' => $entry->slug,
						'athleteBio' => $entry->athleteBio,
						'group'  => !empty($group) ? [
							'title' => $group['title'],
							'id' => $group['id'],
							'uri' => $group['uri'],
							'slug' => $group['slug'],
						] : null,
					];
				},
				'pretty' => true,
			];
		},
		'athletes/<entryId:\d+>.json' => function($entryId) {
			\Craft::$app->response->headers->set('Access-Control-Allow-Origin', '*');
			return [
				'elementType' => Entry::class,
				'criteria' => ['id' => $entryId],
				'one' => true,
				'transformer' => function(Entry $entry) {
					$groupsA = \craft\elements\Entry::find()->section('groups')->relatedTo($entry)->all();
					foreach ($groupsA as $g) {
						$groups[] = [
							'title' => $g->title,
							'id' => $g->id,
							'url' => $g->url,
							'slug' => $g->slug,
						];
					}
					$relatedProgramsA = \craft\elements\Entry::find()->section('programs')->relatedTo($entry)->all();
					$relatedPrograms = [];
					foreach ($relatedProgramsA as $programm) {
						$relatedPrograms[] = [
							'title' => $programm->title,
							'id' => $programm->id,
							'url' => $programm->url,
							'slug' => $programm->slug,
							'category' => $programm->programCategory->first(),
						];
					}
					$relatedSessionsA = \craft\elements\Entry::find()->section('sessions')->relatedTo($groupsA)->orderBy('schedule asc')->schedule('>= '. date(DATE_ATOM))->limit(9)->all();
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
					$next = \craft\elements\Entry::find()->section('sessions')->relatedTo($groupsA)->orderBy('schedule asc')->schedule('>= '. date('Y-m-d'))->one();
					$scheduleString = !empty($next['schedule']) ? new DateTime($next['schedule']->format(DATE_ATOM)) : null;
					return [
						'debug' => $groupsA[0]['id'],
						'title' => $entry->title,
						'id' => $entry->id,
						'url' => $entry->url,
						'slug' => $entry->slug,
						'athleteBio' => $entry->athleteBio,
						'groups'  => !empty($groups) ? $groups : null,
						'programs' => $relatedPrograms,
						'sessions' => $relatedSessions,
						'nextSession' => !empty($next) ? [
							'title' => $next['title'],
							'id' => $next['id'],
							'uri' => $next['uri'],
							'slug' => $next['slug'],
							'schedule' => $next['schedule'],
							'fromNow' => date_diff($scheduleString,new DateTime())->format('P%yY%mM%dDT%hH%mM%sS'),
							'fromNowString' => $scheduleString->diff(new DateTime())->format("%d jours %hh et %im"),
						] : null,
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
					$relatedSessionsA = \craft\elements\Entry::find()->section('sessions')->relatedTo($entry)->orderBy('schedule asc')->schedule('>= '. date(DATE_ATOM))->limit(5)->all();
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
					$next = \craft\elements\Entry::find()->section('sessions')->relatedTo($entry)->orderBy('schedule asc')->schedule('>= '. date('Y-m-d'))->one();
					$scheduleString = !empty($next['schedule']) ? new DateTime($next['schedule']->format(DATE_ATOM)) : null;
					$scheduleBeforeNow = !empty($next['schedule'] and strtotime($next['schedule']->format(DATE_ATOM)) < strtotime('now')) ? true
					 : false;
					return [
						'title' => $entry->title,
						'id' => $entry->id,
						'url' => $entry->url,
						'slug' => $entry->slug,
						'groupDescription' => $entry->groupDescription,
						'sessions' => $relatedSessions,
						'nextSession' => !empty($next) ? [
							'title' => $next['title'],
							'id' => $next['id'],
							'uri' => $next['uri'],
							'slug' => $next['slug'],
							'schedule' => $next['schedule'],
							'fromNow' => date_diff($scheduleString, new DateTime())->format('P%yY%mM%dDT%hH%mM%sS'),
							'fromNowString' => $scheduleString->diff(new DateTime())->format("%d jours %hh et %im"),
							'beforeNow' => $scheduleBeforeNow,
						] : null,
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
					$relatedAthletesA = \craft\elements\Entry::find()->relatedTo($entry)->section('athletes')->all();
					$relatedAthletes = [];
					foreach ($relatedAthletesA as $athlete) {
						$relatedAthletes[] = [
							'title' => $athlete->title,
							'id' => $athlete->id,
							'url' => $athlete->url,
							'uri' => $athlete->uri,
							'slug' => $athlete->slug,
						];
					}
					$relatedSessionsA = \craft\elements\Entry::find()->section('sessions')->relatedTo($entry)->orderBy('schedule desc')->schedule('>= '. date(DATE_ATOM))->limit(9)->all();
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
					$next = \craft\elements\Entry::find()->section('sessions')->relatedTo($entry)->orderBy('schedule asc')->schedule('>= '. date('Y-m-d'))->one();
					return [
						'title' => $entry->title,
						'id' => $entry->id,
						'url' => $entry->url,
						'slug' => $entry->slug,
						'groupDescription' => $entry->groupDescription,
						'athletes' => $relatedAthletes,
						'sessions' => $relatedSessions,
						'nextSession' => !empty($next) ? [
							'title' => $next['title'],
							'id' => $next['id'],
							'uri' => $next['uri'],
							'slug' => $next['slug'],
							'schedule' => $next['schedule'],
							'uri' => $next['uri'],
							'uri' => $next['uri'],
						] : null,
					];
				},
				'pretty' => true,
			];
		},
		'programs.json' => function() {
			\Craft::$app->response->headers->set('Access-Control-Allow-Origin', '*');
			return [
				'elementType' => Entry::class,
				'criteria' => ['section' => 'programs'],
				'transformer' => function(Entry $entry) {
					$tagsA = \craft\elements\Tag::find()->relatedTo($entry)->all();
					$tags = [];
					foreach ($tagsA as $t) {
						$tags[] = [
							'title' => $t->title,
							'id' => $t->id,
							'slug' => $t->slug,
						];
					}
					$categoriesA = \craft\elements\Category::find()->relatedTo($entry)->all();
					foreach ($categoriesA as $cat) {
						$categories[] = [
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
						'categories' => !empty($categories) ? $categories : null,
						'tags' => !empty($tags) ? $tags : null,
					];
				},
				'pretty' => true,
			];
		},
		'programs/<entryId:\d+>.json' => function($entryId) {
			\Craft::$app->response->headers->set('Access-Control-Allow-Origin', '*');
			return [
				'elementType' => Entry::class,
				'criteria' => ['id' => $entryId],
				'one' => true,
				'transformer' => function(Entry $entry) {
					$tagsA = \craft\elements\Tag::find()->relatedTo($entry)->all();
					$tags = [];
					foreach ($tagsA as $t) {
						$tags[] = [
							'title' => $t->title,
							'id' => $t->id,
							'slug' => $t->slug,
						];
					}
					$categoriesA = \craft\elements\Category::find()->relatedTo($entry)->all();
					$categories = [];
					foreach ($categoriesA as $cat) {
						$categories[] = [
							'id' => $cat->id,
							'title' => $cat->title,
							'slug' => $cat->slug,
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
								$exercisesA = $block->exercises->all();
								$exercises = [];
								foreach ($exercisesA as $ex) {
									$relatedE = \craft\elements\Entry::find()->relatedTo($ex)->one();
									$illustration = \craft\elements\Asset::find()->relatedTo($relatedE)->one();
									$relatedEProps = $relatedEProps = [
										'title' => $relatedE['title'],
										'id' => $relatedE['id'],
										'url' => $relatedE['url'],
										'uri' => $relatedE['uri'],
										'slug' => $relatedE['slug'],
										'exerciseDescription' => $relatedE['exerciseDescription'],
										'note' => $relatedE['note'],
										'difficulty' => $relatedE['difficulty'],
										'exerciseIntensity' => $relatedE['exerciseIntensity'],
										'illustration' => $illustration,
									];
									$duration = ($ex->series ? $ex->series.'x': null).($ex->repetitions ? $ex->repetitions: null).($ex->series == null && $ex->repetitions ? 'x': null).($ex->rest ? ' ['.$ex->rest.']': null);
									$exercises[] = [
										'exercise' => $ex->exercise,
										'series' => $ex->series,
										'repetitions' => $ex->repetitions,
										'rest' => $ex->rest,
										'duration' => $duration,
										'note' => $ex->note,
										'exerciseRelation' => !empty($relatedE) ? $relatedEProps : null
									];
								}
								$blockMatrix[] = [
									'heading' => $informations->heading,
									'note' => $informations->note,
									'time' => $informations->time,
									'rest' => $informations->rest,
									'exercises' => $exercises
								];
								break;
								case 'blockType':
								$blockType = $block->blockType->first();
								foreach ($blockType->getFieldValue('block')->all() as $b) {
									$informations = $b->informations->first();
									$exercisesA = $b->exercises->all();
									$exercises = [];
									foreach ($exercisesA as $ex) {
										$relatedE = \craft\elements\Entry::find()->relatedTo($ex)->one();
										$illustration = \craft\elements\Asset::find()->relatedTo($relatedE)->one();
										$relatedEProps = $relatedEProps = [
											'title' => $relatedE['title'],
											'id' => $relatedE['id'],
											'url' => $relatedE['url'],
											'uri' => $relatedE['uri'],
											'slug' => $relatedE['slug'],
											'exerciseDescription' => $relatedE['exerciseDescription'],
											'note' => $relatedE['note'],
											'difficulty' => $relatedE['difficulty'],
											'exerciseIntensity' => $relatedE['exerciseIntensity'],
											'illustration' => $illustration,
										];
										$duration = ($ex->series ? $ex->series.'x': null).($ex->repetitions ? $ex->repetitions: null).($ex->series == null && $ex->repetitions ? 'x': null).($ex->rest ? ' ['.$ex->rest.']': null);
										$exercises[] = [
											'exercise' => $ex->exercise,
											'series' => $ex->series,
											'repetitions' => $ex->repetitions,
											'rest' => $ex->rest,
											'duration' => $duration,
											'note' => $ex->note,
											'exerciseRelation' => !empty($relatedE) ? $relatedEProps : null
										];
									}
									$blockMatrix[] = [
										'heading' => $informations->heading,
										'note' => $informations->note,
										'time' => $informations->time,
										'rest' => $informations->rest,
										'exercises' => $exercises
									];
								}
								break;
							}
						}
					}
					return [
						'title' => $entry->title,
						'id' => $entry->id,
						'url' => $entry->url,
						'slug' => $entry->slug,
						'description' => !empty($entry->description) ? $entry->description : null,
						'blocks'  => $entry->block ? $blockMatrix : null,
						'categories' => !empty($categories) ? $categories : null,
						'tags' => !empty($tags) ? $tags : null,
					];
				},
				'pretty' => true,
			];
		},
		'exercises.json' => function() {
			\Craft::$app->response->headers->set('Access-Control-Allow-Origin', '*');
			return [
				'elementType' => Entry::class,
				'criteria' => ['section' => 'exercises', 'orderBy' => 'title'],
				'paginate' => false,
				'transformer' => function(Entry $entry) {
					$exerciseTagA = \craft\elements\Tag::find()->relatedTo($entry)->all();
					foreach ($exerciseTagA as $t) {
						$tags[] = [
							'title' => $t->title,
							'id' => $t->id,
							'slug' => $t->slug,
						];
					}
					return [
						'title' => $entry->title,
						'alias' => $entry->exerciseAlias,
						'id' => $entry->id,
						'url' => $entry->url,
						'slug' => $entry->slug,
						'tags' => !empty($tags) ? $tags : null,
					];
				},
				'pretty' => true,
			];
		},
		'exercises/<entryId:\d+>.json' => function($entryId) {
			\Craft::$app->response->headers->set('Access-Control-Allow-Origin', '*');
			return [
				'elementType' => Entry::class,
				'criteria' => ['id' => $entryId],
				'one' => true,
				'transformer' => function(Entry $entry) {
					$illustration = \craft\elements\Asset::find()->relatedTo($entry)->one();
					$typeA = $entry->exerciseType->all();
					foreach ($typeA as $e) {
						$type[] = [
							'title' => $e->title,
							'id' => $e->id,
							'slug' => $e->slug,
						];
					}
					$easierA = $entry->exerciseEasier->all();
					foreach ($easierA as $e) {
						$easier[] = [
							'title' => $e->title,
							'id' => $e->id,
							'slug' => $e->slug,
						];
					}
					$harderA = \craft\elements\Entry::find()->relatedTo(array('targetElement' => $entry, 'field' => 'exerciseEasier'))->all();
					foreach ($harderA as $e) {
						$harder[] = [
							'title' => $e->title,
							'id' => $e->id,
							'slug' => $e->slug,
						];
					}
					$variationA = \craft\elements\Entry::find()->relatedTo(array('element' => $entry, 'field' => 'exerciseVariation'))->all();
					foreach ($variationA as $e) {
						$variation[] = [
							'title' => $e->title,
							'id' => $e->id,
							'slug' => $e->slug,
						];
					}
					$muscleTargetA = $entry->muscleTarget->all();
					foreach ($muscleTargetA as $e) {
						$muscleTarget[] = [
							'title' => $e->title,
							'id' => $e->id,
							'slug' => $e->slug,
						];
					}
					$materialA = \craft\elements\Category::find()->relatedTo($entry->minimumAge->all())->all();
					foreach ($materialA as $e) {
						$material[] = [
							'title' => $e->title,
							'id' => $e->id,
							'slug' => $e->slug,
						];
					}
					$minimumAge = $entry->minimumAge->one();
					$tagA = \craft\elements\Tag::find()->relatedTo($entry)->all();
					foreach ($tagA as $t) {
						$tags[] = [
							'title' => $t->title,
							'id' => $t->id,
							'slug' => $t->slug,
						];
					}
					return [
						'title' => $entry->title,
						'alias' => $entry->exerciseAlias,
						'id' => $entry->id,
						'url' => $entry->url,
						'slug' => $entry->slug,
						'type' => !empty($type) ? $type : null,
						'description' => $entry->exerciseDescription,
						'note' => $entry->note,
						'tags' => !empty($tags) ? $tags : null,
						'difficulty' => !empty($entry->difficulty->value != null) ? $entry->difficulty : null,
						'intensity' => !empty($entry->exerciseIntensity->value != null) ? $entry->exerciseIntensity : null,
						'illustration' => $illustration,
						'easier' => !empty($easier) ? $easier : null,
						'harder' => !empty($harder) ? $harder : null,
						'variation' => !empty($variation) ? $variation : null,
						'material' => !empty($material) ? $material : null,
						'muscleTarget' => !empty($muscleTarget) ? $muscleTarget : null,
						'minimumAge' => $minimumAge,
						'reference' => $entry->exerciseReference,
					];
				},
				'pretty' => true,
			];
		},

	]
];
