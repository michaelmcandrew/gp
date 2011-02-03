SELECT cc.organization_name AS local_party_name, cc.id AS local_party_contact_id, w.code AS ons_code, w.name AS ward_name, b.name AS DC_MD_UA_LB_name, a.name AS county_name
FROM ons AS w
LEFT JOIN ons AS b ON w.code_4=b.code
LEFT JOIN ons AS a ON w.code_2=a.code
LEFT JOIN ukgr_crm.civicrm_gpew_ward_local_party AS cwlp ON w.code=cwlp.ward_ons_code
LEFT JOIN ukgr_crm.civicrm_contact cc ON cwlp.local_party_contact_id=cc.id
WHERE (w.country='E' OR w.country='W') AND length(w.code)=6
ORDER BY cc.organization_name IS NULL, cc.organization_name, w.code;


-- SELECT cc.organization_name AS local_party_name, cc.id AS local_party_contact_id, w.code AS ons_code, w.name AS ward_name, b.name AS DC_MD_UA_LB_name, a.name AS county_name, b.type
-- FROM ons AS w
-- LEFT JOIN ons AS b ON w.code_4=b.code
-- LEFT JOIN ons AS a ON w.code_2=a.code
-- LEFT JOIN ukgr_crm.civicrm_gpew_ward_local_party AS cwlp ON w.code=cwlp.ward_ons_code
-- LEFT JOIN ukgr_crm.civicrm_contact cc ON cwlp.local_party_contact_id=cc.id
-- WHERE (w.country='E' OR w.country='W') AND length(w.code)=6
-- GROUP BY b.type
-- ORDER BY cc.organization_name IS NULL, cc.organization_name, w.code
