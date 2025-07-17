# Kochbuch-DB
Updated Version of "Kochbuch"


Table Gerichte:
 - id
 - titel
 - kurzbeschreibung
 - timecode
 - bild_1
 - bild_2
 - bild_3
 - tags
 - personenanzahl
 - rating1star
 - rating2star
 - rating3star
 - rating4star
 - rating5star
 - VZeit (Vorbereitung)
 - ZZeit (Zubereitung)
 - made_by_user
 - viewcount
 - timecode
 - error_msg

Table Zutaten:
 - id
 - gericht_id
 - menge
 - einheit
 - Zutat

Tabelle Schritte:
 - id
 - gerichte_id
 - Schritt