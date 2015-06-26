<?php

namespace Divisions\Doctrine\DBAL\Logging\Tracy;

use Nette\Database\Helpers;

/**
 * Class Spot2Panel
 * @package Divisions\Doctrine\DBAL\Logging\Tracy
 */
class Spot2Panel
	extends \Doctrine\DBAL\Logging\DebugStack
	implements \Tracy\IBarPanel{

	/**
	 * @var int
	 */
	private $totalTime = 0;

	/**
	 * @return string
	 */
	public function getTab(){
		return '<span title="Spot2">
		<svg viewBox="0 0 2048 2048"><path fill="'.($this->queries ? '#469ED6' : '#A0B2BE').'" d="M1024 896q237 0 443-43t325-127v170q0 69-103 128t-280 93.5-385 34.5-385-34.5-280-93.5-103-128v-170q119 84 325 127t443 43zm0 768q237 0 443-43t325-127v170q0 69-103 128t-280 93.5-385 34.5-385-34.5-280-93.5-103-128v-170q119 84 325 127t443 43zm0-384q237 0 443-43t325-127v170q0 69-103 128t-280 93.5-385 34.5-385-34.5-280-93.5-103-128v-170q119 84 325 127t443 43zm0-1152q208 0 385 34.5t280 93.5 103 128v128q0 69-103 128t-280 93.5-385 34.5-385-34.5-280-93.5-103-128v-128q0-69 103-128t280-93.5 385-34.5z"/><span class="tracy-label">'.($this->totalTime ? sprintf('%0.1f ms | ',
		                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 $this->totalTime) : '').count($this->queries).'</span></span>';
	}


	/**
	 * @return string
	 */
	public function getPanel(){
		$output = '';

		foreach($this->queries AS $record){
			$time   = $record['executionMS'];
			$query  = $record['sql'];
			$params = array_values($record['params']);
			$output .= '<tr><td>';
			$output .= sprintf('%0.3f ms ', $time);

			$output .= '</td><td>'.Helpers::dumpSql($query, $params);

			$output .= '</td></tr>';
		}

		return empty($this->queries) ? '' : '<style> #tracy-debug .tracy-Spot2Panel tr table { margin: 8px 0; max-height: 150px; overflow:auto }</style>
			<h1>Queries: '.htmlSpecialChars(count($this->queries),
		                                    ENT_QUOTES,
		                                    'UTF-8').' | Time: '.htmlSpecialChars(sprintf('%0.1f ms',
		                                                                                  $this->totalTime),
		                                                                          ENT_QUOTES,
		                                                                          'UTF-8').'</h1>
			<div class="tracy-inner tracy-Spot2Panel">
			<table>
				<tr><th>Time</th><th>SQL Statement</th></tr>'.$output.'
			</table>
			</div>';
	}

	/**
	 * {@inheritdoc}
	 */
	public function stopQuery(){
		parent::stopQuery();
		$this->totalTime += $this->queries[$this->currentQuery]['executionMS'];
	}
}