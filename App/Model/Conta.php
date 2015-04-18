<?php
use Livro\Database\Record;
use Livro\Database\Criteria;
use Livro\Database\Repository;
use Livro\Database\Filter;

class Conta extends Record
{
    const TABLENAME = 'conta';
	
	public static function getByPessoa($id_pessoa)
	{
	    $criteria = new Criteria;
	    $criteria->add(new Filter('paga', '<>', 'S'));
	    $criteria->add(new Filter('id_cliente', '=', $id_pessoa));
	    
	    $repo = new Repository('Conta');
	    return $repo->load($criteria);
	}
	
	public static function debitosPorPessoa($id_pessoa)
	{
	    $total = 0;
	    $contas = self::getByPessoa($id_pessoa);
	    if ($contas)
	    {
	        foreach ($contas as $conta)
	        {
	            $total += $conta->valor;
	        }
	    }
	    return $total;
	}
	
	public static function geraParcelas($id_cliente, $delay, $valor, $parcelas)
	{
	    $date = new DateTime(date('Y-m-d'));
	    $date->add(new DateInterval('P'.$delay.'D'));
	    
	    for ($n=1; $n<=$parcelas; $n++)
	    {
	        $conta = new self;
	        $conta->id_cliente = $id_cliente;
	        $conta->dt_emissao = date('Y-m-d');
	        $conta->dt_vencimento = $date->format('Y-m-d');
	        $conta->valor = $valor / $parcelas;
	        $conta->paga = 'N';
	        $conta->store();
	        
	        $date->add(new DateInterval('P1M'));
	    }
	}
}