-- SQL komandos esamų prizų paveikslėlių atnaujinimui
-- Paleiskite šias komandas, jei nenorite perkrauti fixtures (doctrine:fixtures:load)

UPDATE reward SET image = '/images/rewards/spalvinimo-knygele.svg' WHERE name = 'Spalvinimo knygelė';
UPDATE reward SET image = '/images/rewards/lipdukai.svg' WHERE name = 'Lipdukai';
UPDATE reward SET image = '/images/rewards/knyga-dovana.svg' WHERE name = 'Knyga-dovana';
UPDATE reward SET image = '/images/rewards/zaisliukas.svg' WHERE name = 'Žaisliukas';
UPDATE reward SET image = '/images/rewards/specialus-zenkliukas.svg' WHERE name = 'Specialus ženkliukas';
