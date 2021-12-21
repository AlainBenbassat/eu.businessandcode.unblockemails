<?php

class CRM_Unblockemails_Helper {
  public function getDaysWithEmailsOnHold($numOnHold = 1) {
    $list = [];

    $sql = "
      select
        date_Format(hold_date, '%Y-%m-%d') onhold_date,
        count(*) on_hold_count
      from
        civicrm_email
      where
        on_hold = 1
      group by
        date_Format(hold_date, '%Y-%m-%d')
      having
        count(*) >= $numOnHold
      order by
        1 desc
      limit 0,20
    ";
    $dao = CRM_Core_DAO::executeQuery($sql);

    while ($dao->fetch()) {
      $list[$dao->onhold_date] = $dao->onhold_date . ' (' . $dao->on_hold_count . ')';
    }

    return $list;
  }

  public function unblockEmailsOnDate($onholdDate) {
    $fromDate = "$onholdDate 00:00:00";
    $toDate = "$onholdDate 23:59:59";

    $sql = "
      update
        civicrm_email
      set
        hold_date = NULL,
        reset_date = NOW(),
        on_hold = 0
      where
        on_hold = 1
      and
        hold_date between %1 and %2
    ";
    $sqlParams = [
      1 => [$fromDate, 'String'],
      2 => [$toDate, 'String'],
    ];

    CRM_Core_DAO::executeQuery($sql, $sqlParams);
  }
}
