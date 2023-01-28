<?php

namespace Site\Util;


class Watcher
{
  public static $system = null;

  public static function onCreate($entity, $item, $data)
  {
    // if ($entity === 'Post') {
    //   if ($data['classification'] === 'ITEM') {
    //     self::addProfilePoints($data['user'], "New Item Post ({$item->id})", 'NEW_ITEM', $item->id, self::getSystem()->newItemPoints);
    //   }
    //   if ($data['classification'] === 'FAVOR') {
    //     self::addProfilePoints($data['user'], "New Favor Post ({$item->id})", 'NEW_FAVOR', $item->id, self::getSystem()->newFavorPoints);
    //   }
    // }
    // if ($entity === 'Offer') {
    //   if ($data['classification'] === 'ITEM') {
    //     self::addProfilePoints($data['user'], "New Item Offer ({$item->id})", 'NEW_ITEM_OFFER', $item->id, self::getSystem()->newItemOfferPoints);
    //   }
    //   if ($data['classification'] === 'FAVOR') {
    //     self::addProfilePoints($data['user'], "New Favor Offer ({$item->id})", 'NEW_FAVOR_OFFER', $item->id, self::getSystem()->newFavorOfferPoints);
    //   }
    // }
  }

  public static function onUpdate($entity, $item, $data)
  {
    // if ($entity === 'PostOffer') {
    //   if (in_array($data['status'], ['CANCELED', 'DENIED'])) {
    //     $offer = Offer::get($item->offerId);
    //     $type = $offer->classification === 'ITEM' ? 'NEW_ITEM_OFFER' : 'NEW_FAVOR_OFFER';
    //     $rejecting = $offer->classification === 'ITEM' ? 'Item' : 'Favor';
    //     $obj = ProfilePoints::select()->where([
    //       "type" => $type,
    //       "itemId" => $offer->id,
    //     ])->limit(1)->first();
    //     self::addProfilePoints($obj->userId, "{$rejecting} Offer Denied ({$item->id})", $type, $item->id, intval($obj->points) * -1);
    //   }
    // }
  }
}
