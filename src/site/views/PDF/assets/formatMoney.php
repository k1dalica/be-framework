<?php

function formatMoney($money)
{
  return "$" . number_format($money, 2, '.', ',');
}
