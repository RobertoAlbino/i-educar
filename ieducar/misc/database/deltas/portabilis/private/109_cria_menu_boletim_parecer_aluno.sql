
-- @author   Alan Felipe Farias <alan@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into portal.menu_submenu values (999824, 55, 2,'Parecer descritivo por etapa', 'module/Reports/BoletimParecerAluno', null, 3);
insert into pmicontrolesis.menu values (999824, 999824, 999450, 'Parecer descritivo por etapa', 0, 'module/Reports/BoletimParecerAluno', '_self', 1, 15, 192, null);
insert into pmieducar.menu_tipo_usuario values(1,999824,1,1,1);

--undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999824;
delete from pmicontrolesis.menu where cod_menu = 999824;
delete from portal.menu_submenu where cod_menu_submenu = 999824;
