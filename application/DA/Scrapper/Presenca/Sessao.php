<?php

namespace DA\Scrapper\Presenca;
use DA\Scrapper\Presenca;

class Sessao extends Presenca
{
    /**
     * Extrai os dados das Presencas em Sessoes do Plenario
     * @param  int $deputadoId id do Deputado
     * @param  array $urlParams  Parametros a serem substituidos na url
     * 
     *  $urlParams = array(
     *    'legislatura'    => Numero da legislatura, 
     *    'last3Matricula' => Ultimos tres digitos da Matricula,
     *    'dataInicio'     => Data de Inicio da Extracao, 
     *    'dataFim'        => Data de Fim da Extracao, 
     *    'numero'         => Numero do Deputado
     *  );
     * 
     * @return array              dados da Presenca
     */
    public function getPresencas($deputadoId, $urlParams=array())
    {
        $url = str_replace(
                    array(
                        '%legislatura%',
                        '%last3Matricula%',
                        '%dataInicio%',
                        '%dataFim%'
                    ),
                    array(
                        $urlParams['legislatura'],
                        $urlParams['last3Matricula'],
                        $urlParams['dataInicio'],
                        $urlParams['dataFim']
                    ),
                    $this->app['config']['url.presencaPlenario']
                );
        
        $this->app['monolog']->addInfo(sprintf("Iniciando a extracao para a url %s.", $url));
        
        $crawler = $this->request($url);
        
        $presencas   = $crawler->filter('table.tabela-1 > tr'); // > tbody > tr
        
        if(count($presencas) == 0) {
            $presencas   = $crawler->filter('table.tabela-1 > tbody > tr');
        }
        
        if(count($presencas) == 0) {
            $this->app['monolog']->addInfo(sprintf("Nenhuma informacao encontrada %s.", $url));
            return array();
        }
        
        $dataPresencas = array();
        $pres = array();
        $pres['deputadoId'] = $deputadoId;
        
        foreach ($presencas as $node) {
            $tds = $node->getElementsByTagName('td');
            if($node->getAttribute('class') == "even") {
                $dates = explode('/', trim($tds->item(0)->nodeValue));
                $pres['data'] = $dates[2].'-'.$dates[1].'-'.$dates[0];
                $pres['justificativa']  = utf8_decode(trim($tds->item(2)->nodeValue));
            } else {
                $pres['titulo']         = substr(utf8_decode(trim($tds->item(0)->nodeValue)), 6);
                $comportamento          = utf8_decode(trim($tds->item(1)->nodeValue));
                $pres['comportamento']  = substr($comportamento,0, strlen($comportamento)-1);
                
                $dataPresencas[] = $pres;
            }
        }
        return $dataPresencas;
    }
    
}