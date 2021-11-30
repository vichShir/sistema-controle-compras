/* TRIGGERS */

/* Toda vez que um item de nota fiscal for incluído, 
seja atualizada o valor total da nota fiscal; */
CREATE TRIGGER inclusaoitemnotafiscal
ON itemnotafiscal FOR INSERT
AS
	/* Atualizar valor total da nota fiscal */
	DECLARE @valortotal AS NUMERIC(8, 2) = (SELECT (i.quantidade * p.valorunitario) - i.desconto 
     	                           			FROM produto p INNER JOIN inserted i 
            		                    		ON p.codproduto = i.codproduto);

	UPDATE notafiscal
	SET valorpagar = valorpagar + @valortotal
   	WHERE numnota = (SELECT numnota FROM inserted)
   	IF @@rowcount > 0 -- atualização do valor total bem sucedida
	BEGIN
		UPDATE itemnotafiscal
		SET valortotal = @valortotal
	   	WHERE numnota = (SELECT numnota FROM inserted) AND
		codproduto = (SELECT codproduto FROM inserted)
		IF @@rowcount = 0 -- atualização do valor total mal sucedida
			rollback transaction
	END
	ELSE
  	   rollback transaction
GO

/* b) Toda vez que um item de nota fiscal for excluído, 
seja atualizada o valor total da nota fiscal; */
CREATE TRIGGER exclusaoitemnotafiscal
ON itemnotafiscal FOR DELETE
AS 
   	/* Atualizar valor total da nota fiscal */
	UPDATE notafiscal
    SET valorpagar = valorpagar - (SELECT d.quantidade * p.valorunitario 
     	                           FROM produto p INNER JOIN deleted d
            		                    ON p.codproduto = d.codproduto)
   	WHERE numnota = (SELECT numnota FROM deleted)
	IF @@rowcount = 0 -- atualização do valor total mal sucedida
		rollback transaction
GO

/* c) Toda vez que for alterada a quantidade de um item de nota fiscal,
seja atualizada o valor total da nota fiscal; */
CREATE TRIGGER alteracaoitemnotafiscal
ON itemnotafiscal FOR UPDATE
AS 
IF UPDATE(quantidade)
BEGIN
	/* Atualizar valor total da nota fiscal */
	DECLARE @valortotal AS NUMERIC(8, 2) = (SELECT p.valorunitario * (i.quantidade - d.quantidade)
                                   FROM produto p INNER JOIN inserted i                  
                                        ON p.codproduto = i.codproduto 
                                        INNER JOIN deleted d              
                                        ON i.codproduto = d.codproduto AND 
                                          i.numnota = d.numnota);

   	UPDATE notafiscal
   	SET valorpagar = valorpagar + @valortotal
   	WHERE numnota = (SELECT numnota FROM inserted)
	IF @@rowcount > 0 -- atualização do valor total bem sucedida
	BEGIN
		UPDATE itemnotafiscal
		SET valortotal = @valortotal
	   	WHERE numnota = (SELECT numnota FROM inserted) AND
		codproduto = (SELECT codproduto FROM inserted)
		IF @@rowcount = 0 -- atualização do valor total mal sucedida
			rollback transaction
	END
	ELSE
  	   rollback transaction
END
GO

/* d) Toda vez que for alterada a quantidade de um item de nota fiscal,
seja atualizada o valor total da nota fiscal; */
CREATE TRIGGER alteracaonotafiscal
ON notafiscal FOR UPDATE
AS 
IF UPDATE(desconto)
BEGIN
	/* Atualizar valor total da nota fiscal */
	DECLARE @valorpagar AS NUMERIC(8, 2) = (SELECT (n.valorpagar - (i.desconto - d.desconto))
											FROM notafiscal n INNER JOIN inserted i
												ON n.numnota = i.numnota
												INNER JOIN deleted d
												ON i.numnota = d.numnota);

   	UPDATE notafiscal
   	SET valorpagar = @valorpagar
   	WHERE numnota = (SELECT numnota FROM inserted)
	IF @@rowcount = 0 -- atualização do valor a pagar mal sucedida
		rollback transaction
END
GO

/*
DROP TRIGGER inclusaoitemnotafiscal, exclusaoitemnotafiscal, alteracaoitemnotafiscal, alteracaonotafiscal
*/