<?php
namespace Nethgui\Module\Table;

/*
 * Copyright (C) 2011 Nethesis S.r.l.
 * 
 * This script is part of NethServer.
 * 
 * NethServer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * NethServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with NethServer.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * A table action is provided with an adapter to access the underlying database table
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 * @api
 */
interface TableActionInterface
{
    /**
     * @param \Nethgui\Adapter\AdapterInterface $tableAdapter
     * @return TableActionInterface
     */
    public function setTableAdapter(\Nethgui\Adapter\AdapterInterface $tableAdapter);


    /**
     * @return bool
     */
    public function hasTableAdapter();
}