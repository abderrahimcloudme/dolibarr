-- ============================================================================
-- Copyright (C) 2009 Laurent Destailleur <eldy@users.sourceforge.net>
-- Copyright (C) 2009 Regis Houssin       <regis.houssin@capnetworks.com>
--
-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program. If not, see <http://www.gnu.org/licenses/>.
--
-- ============================================================================


ALTER TABLE llx_virement_cmde ADD UNIQUE INDEX uk_virement_cmde_fk_commande (fk_commande, date_mvt,num_mvt);
ALTER TABLE llx_virement_cmde ADD CONSTRAINT virement_cmde_fk_commande FOREIGN KEY (fk_commande) REFERENCES llx_commande (rowid);

