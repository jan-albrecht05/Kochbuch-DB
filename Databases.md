## gerichte.db
Table Gerichte:
 - id
 - titel
 - kurzbeschreibung
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
 - timecode_erstellt
 - timecode_lastchange
 - error_msg
 - status

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


## users.db
Table users:
 - id
 - name
 - password
 - Profilbild
 - Rolle (user, editor, admin)
 - badge (newcommer, profi, ..)
 - saved recepies

Table Einlaufsliste
 - id
 - user id
 - menge
 - einheit
 - zutat
 - status (0 = offen, 1 = abgehakt)