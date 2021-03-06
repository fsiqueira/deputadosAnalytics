SELECT 
	count(*), d.nome, WEEK(ps.data)
FROM 
	presencasessao ps
JOIN 
	deputado d ON (d.id = ps.deputadoId)
WHERE
	ps.comportamento = 'PresenÃ§a'
GROUP BY
	ps.deputadoId, WEEK(ps.data)