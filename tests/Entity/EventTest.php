<?php

namespace Radar\Connect\Tests\Entity;

use Radar\Connect\Entity\Event;

class EventTest extends EntityTestCase {
  public function testRequestParse() {
    $response = $this->getMockResponse('event');
    $event = $this->parseResponse($response);

    $this->assertEquals(count($event), 1);
    $event = reset($event);
    // Basic properties
    $this->assertEquals($event->getUuid(), '05263181-c2cc-47cc-8d4d-772c8289bf57');
    $this->assertEquals($event->getVuuid(), '18357655-66c1-43c1-bca2-7ee65670f27c');
    $this->assertEquals($event->getInternalId(), '6773');
    $this->assertEquals($event->getInternalVid(), '7022');
    // Node level fields
    $this->assertEquals($event->apiUri(), 'https://new-radar.squat.net/api/1.0/node/05263181-c2cc-47cc-8d4d-772c8289bf57');
    $body_text = "<p>Monday-Friday: 12:00 - 14:00 <strong>Vokü (Volksküche) Vegan/Vegetarisch (bitte nachfragen)</strong></p>\n<p>\"S’het so lang s’het.\"</p>\n";
    $this->assertEquals($event->getBody(), $body_text);
    $this->assertEquals($event->getBodyRaw(), array('value' => $body_text, 'summary' => '', 'format' => 'rich_text_editor'));
    $this->assertEquals($event->getUrlView(),'http://radar.d7.montseny.iskranet/en/event/basel/schanze/2015-02-20/vok%C3%BC-volksk%C3%BCche');
    $this->assertEquals($event->getUrlEdit(),'http://radar.d7.montseny.iskranet/en/node/6773/edit');
    $this->assertEquals($event->getStatus(), TRUE);
    $this->assertEquals($event->getCreated()->getTimestamp(),'1422281292');
    $this->assertEquals($event->getUpdated()->getTimestamp(),'1424519769');
    // Node level references
    $categories = $event->getCategories();
    $this->assertTrue($categories[0] instanceof \Radar\Connect\Entity\TaxonomyTerm);
    $this->assertEquals($categories[0]->apiUri(),'http://radar.d7.montseny.iskranet/api/1.0/taxonomy_term/8e846372-fa86-4cb2-87d1-f24da784ec6b');
    $topics = $event->getTopics();
    $this->assertTrue($topics[0] instanceof \Radar\Connect\Entity\TaxonomyTerm);
    $this->assertEquals($topics[0]->apiUri(), 'http://radar.d7.montseny.iskranet/api/1.0/taxonomy_term/782ad634-f407-47b8-8da9-20c5de7b2a8f');
    // Simple fields.
    $this->assertTrue($event instanceof Event);
    $this->assertEquals($event->getTitle(), 'Vokü (Volksküche)');
    //$this->assertEquals($event->getImageRaw(), '');
    $this->assertEquals($event->getPrice(), array('by donation'));
    $this->assertEquals($event->getEmail(), 'user@example.com');
    $this->assertEquals($event->getLinkRaw(), array(array('url' => 'http://example.com', 'attributes' => array())));
    $this->assertEquals($event->getLink(), array('http://example.com'));
    $this->assertEquals($event->getPhone(), '1234565');
    // Entity references.
    $groups = $event->getGroups();
    $this->assertTrue($groups[0] instanceof \Radar\Connect\Entity\Group);
    $this->assertEquals($groups[0]->apiUri(), 'http://radar.d7.montseny.iskranet/api/1.0/node/06a0ab83-434f-46e8-9d0e-b8ef8e6cd995');
    $raw_dates = $event->getDatesRaw();
    $this->assertEquals($raw_dates[0]['value'], '1424430000');
    $this->assertEquals($raw_dates[0]['time_end'], '2015-02-20T13:30:00+01:00');
    $dates = $event->getDates();
    $this->assertTrue($dates[0]['start'] instanceof \DateTime);
    $this->assertEquals($dates[0]['start']->getTimestamp(), '1424430000');
    $this->assertEquals($dates[0]['end']->getTimezone()->getName(), '+01:00');
    $locations = $event->getLocations();
    $this->assertTrue($locations[0] instanceof \Radar\Connect\Entity\Location);
    $this->assertEquals($locations[0]->apiUri(), 'http://radar.d7.montseny.iskranet/api/1.0/location/87ed6f6a-24b9-44f2-84f5-698e780abe0d');
  }
}
