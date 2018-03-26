<?php

namespace Erp\PropertyBundle\DataFixtures\ORM;

use Erp\CoreBundle\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CityFixture extends Fixture
{
    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {

        $citiesJSON = '[
  {
      "name": "Sacramento",
    "state_code": "CA",
    "zip": "95811",
    "latitude": 38.5815719,
    "longitude": -121.4943996,
    "country": "Sacramento"
  },
  {
      "name": "Holtsville",
    "state_code": "NY",
    "zip": "00501",
    "latitude": 40.8152,
    "longitude": -73.0455,
    "country": "Suffolk"
  },
  {
      "name": "Adjuntas",
    "state_code": "PR",
    "zip": "00601",
    "latitude": 18.1788,
    "longitude": -66.7516,
    "country": "Adjuntas"
  },
  {
      "name": "Aguada",
    "state_code": "PR",
    "zip": "00602",
    "latitude": 18.381389,
    "longitude": -67.188611,
    "country": "Aguada"
  },
  {
      "name": "Aguadilla",
    "state_code": "PR",
    "zip": "00603",
    "latitude": 18.4554,
    "longitude": -67.1308,
    "country": "Aguadilla"
  },
  {
      "name": "Maricao",
    "state_code": "PR",
    "zip": "00606",
    "latitude": 18.182778,
    "longitude": -66.980278,
    "country": "Maricao"
  },
  {
      "name": "Anasco",
    "state_code": "PR",
    "zip": "00610",
    "latitude": 18.284722,
    "longitude": -67.14,
    "country": "Anasco"
  },
  {
      "name": "Angeles",
    "state_code": "PR",
    "zip": "00611",
    "latitude": 18.286944,
    "longitude": -66.799722,
    "country": "Utuado"
  },
  {
      "name": "Arecibo",
    "state_code": "PR",
    "zip": "00612",
    "latitude": 18.4389,
    "longitude": -66.6924,
    "country": "Arecibo"
  },
  {
      "name": "Bajadero",
    "state_code": "PR",
    "zip": "00616",
    "latitude": 18.428611,
    "longitude": -66.683611,
    "country": "Arecibo"
  },
  {
      "name": "Barceloneta",
    "state_code": "PR",
    "zip": "00617",
    "latitude": 18.4525,
    "longitude": -66.538889,
    "country": "Barceloneta"
  },
  {
      "name": "Boqueron",
    "state_code": "PR",
    "zip": "00622",
    "latitude": 18.028889,
    "longitude": -67.169444,
    "country": "Cabo Rojo"
  },
  {
      "name": "Cabo Rojo",
    "state_code": "PR",
    "zip": "00623",
    "latitude": 18.083611,
    "longitude": -67.148889,
    "country": "Cabo Rojo"
  },
  {
      "name": "Penuelas",
    "state_code": "PR",
    "zip": "00624",
    "latitude": 18.058333,
    "longitude": -66.721944,
    "country": "Penuelas"
  },
  {
      "name": "Camuy",
    "state_code": "PR",
    "zip": "00627",
    "latitude": 18.485833,
    "longitude": -66.845278,
    "country": "Camuy"
  },
  {
      "name": "Castaner",
    "state_code": "PR",
    "zip": "00631",
    "latitude": 18.2165,
    "longitude": -66.8681,
    "country": "Lares"
  },
  {
      "name": "Rosario",
    "state_code": "PR",
    "zip": "00636",
    "latitude": 18.163333,
    "longitude": -67.081667,
    "country": "San German"
  },
  {
      "name": "Sabana Grande",
    "state_code": "PR",
    "zip": "00637",
    "latitude": 18.079722,
    "longitude": -66.960833,
    "country": "Sabana Grande"
  },
  {
      "name": "Ciales",
    "state_code": "PR",
    "zip": "00638",
    "latitude": 18.338056,
    "longitude": -66.469167,
    "country": "Ciales"
  },
  {
      "name": "Utuado",
    "state_code": "PR",
    "zip": "00641",
    "latitude": 18.2675,
    "longitude": -66.700833,
    "country": "Utuado"
  },
  {
      "name": "Dorado",
    "state_code": "PR",
    "zip": "00646",
    "latitude": 18.464444,
    "longitude": -66.270278,
    "country": "Dorado"
  },
  {
      "name": "Ensenada",
    "state_code": "PR",
    "zip": "00647",
    "latitude": 17.968333,
    "longitude": -66.933333,
    "country": "Guanica"
  },
  {
      "name": "Florida",
    "state_code": "PR",
    "zip": "00650",
    "latitude": 18.364444,
    "longitude": -66.561667,
    "country": "Florida"
  },
  {
      "name": "Garrochales",
    "state_code": "PR",
    "zip": "00652",
    "latitude": 18.459722,
    "longitude": -66.611389,
    "country": "Arecibo"
  },
  {
      "name": "Guanica",
    "state_code": "PR",
    "zip": "00653",
    "latitude": 17.973611,
    "longitude": -66.908333,
    "country": "Guanica"
  },
  {
      "name": "Guayanilla",
    "state_code": "PR",
    "zip": "00656",
    "latitude": 18.02,
    "longitude": -66.79,
    "country": "Guayanilla"
  },
  {
      "name": "Hatillo",
    "state_code": "PR",
    "zip": "00659",
    "latitude": 18.488333,
    "longitude": -66.825833,
    "country": "Hatillo"
  },
  {
      "name": "Hormigueros",
    "state_code": "PR",
    "zip": "00660",
    "latitude": 18.14,
    "longitude": -67.128333,
    "country": "Hormigueros"
  },
  {
      "name": "Isabela",
    "state_code": "PR",
    "zip": "00662",
    "latitude": 18.496667,
    "longitude": -67.026389,
    "country": "Isabela"
  },
  {
      "name": "Jayuya",
    "state_code": "PR",
    "zip": "00664",
    "latitude": 18.220556,
    "longitude": -66.591944,
    "country": "Jayuya"
  },
  {
      "name": "Lajas",
    "state_code": "PR",
    "zip": "00667",
    "latitude": 18.05,
    "longitude": -67.061667,
    "country": "Lajas"
  },
  {
      "name": "Lares",
    "state_code": "PR",
    "zip": "00669",
    "latitude": 18.296667,
    "longitude": -66.8775,
    "country": "Lares"
  },
  {
      "name": "Las Marias",
    "state_code": "PR",
    "zip": "00670",
    "latitude": 18.295278,
    "longitude": -67.146667,
    "country": "Las Marias"
  },
  {
      "name": "Manati",
    "state_code": "PR",
    "zip": "00674",
    "latitude": 18.425833,
    "longitude": -66.471667,
    "country": "Manati"
  },
  {
      "name": "Moca",
    "state_code": "PR",
    "zip": "00676",
    "latitude": 18.395,
    "longitude": -67.163333,
    "country": "Moca"
  },
  {
      "name": "Rincon",
    "state_code": "PR",
    "zip": "00677",
    "latitude": 18.34,
    "longitude": -67.25,
    "country": "Rincon"
  },
  {
      "name": "Quebradillas",
    "state_code": "PR",
    "zip": "00678",
    "latitude": 18.475833,
    "longitude": -66.938889,
    "country": "Quebradillas"
  },
  {
      "name": "Mayaguez",
    "state_code": "PR",
    "zip": "00680",
    "latitude": 18.2011,
    "longitude": -67.1172,
    "country": "Mayaguez"
  },
  {
      "name": "San German",
    "state_code": "PR",
    "zip": "00683",
    "latitude": 18.083611,
    "longitude": -67.045278,
    "country": "San German"
  },
  {
      "name": "San Sebastian",
    "state_code": "PR",
    "zip": "00685",
    "latitude": 18.338611,
    "longitude": -66.990556,
    "country": "San Sebastian"
  },
  {
      "name": "Morovis",
    "state_code": "PR",
    "zip": "00687",
    "latitude": 18.327778,
    "longitude": -66.406944,
    "country": "Morovis"
  },
  {
      "name": "Sabana Hoyos",
    "state_code": "PR",
    "zip": "00688",
    "latitude": 18.435833,
    "longitude": -66.614167,
    "country": "Arecibo"
  },
  {
      "name": "San Antonio",
    "state_code": "PR",
    "zip": "00690",
    "latitude": 18.4475,
    "longitude": -66.298056,
    "country": "Aguadilla"
  },
  {
      "name": "Vega Alta",
    "state_code": "PR",
    "zip": "00692",
    "latitude": 18.414167,
    "longitude": -66.331667,
    "country": "Vega Alta"
  },
  {
      "name": "Vega Baja",
    "state_code": "PR",
    "zip": "00693",
    "latitude": 18.4385,
    "longitude": -66.3983,
    "country": "Vega Baja"
  },
  {
      "name": "Yauco",
    "state_code": "PR",
    "zip": "00698",
    "latitude": 18.036944,
    "longitude": -66.850278,
    "country": "Yauco"
  },
  {
      "name": "Aguas Buenas",
    "state_code": "PR",
    "zip": "00703",
    "latitude": 18.258333,
    "longitude": -66.105,
    "country": "Aguas Buenas"
  },
  {
      "name": "Aguirre",
    "state_code": "PR",
    "zip": "00704",
    "latitude": 18.25,
    "longitude": -66.103056,
    "country": "Salinas"
  },
  {
      "name": "Aibonito",
    "state_code": "PR",
    "zip": "00705",
    "latitude": 18.141667,
    "longitude": -66.265,
    "country": "Aibonito"
  },
  {
      "name": "Maunabo",
    "state_code": "PR",
    "zip": "00707",
    "latitude": 18.009167,
    "longitude": -65.899722,
    "country": "Maunabo"
  },
  {
      "name": "Arroyo",
    "state_code": "PR",
    "zip": "00714",
    "latitude": 17.963333,
    "longitude": -66.063333,
    "country": "Arroyo"
  },
  {
      "name": "Mercedita",
    "state_code": "PR",
    "zip": "00715",
    "latitude": 18.0052,
    "longitude": -66.5602,
    "country": "Ponce"
  },
  {
      "name": "Ponce",
    "state_code": "PR",
    "zip": "00716",
    "latitude": 18.015556,
    "longitude": -66.58,
    "country": "Ponce"
  },
  {
      "name": "Naguabo",
    "state_code": "PR",
    "zip": "00718",
    "latitude": 18.213333,
    "longitude": -65.736667,
    "country": "Naguabo"
  },
  {
      "name": "Naranjito",
    "state_code": "PR",
    "zip": "00719",
    "latitude": 18.301667,
    "longitude": -66.246667,
    "country": "Naranjito"
  },
  {
      "name": "Orocovis",
    "state_code": "PR",
    "zip": "00720",
    "latitude": 18.228889,
    "longitude": -66.391389,
    "country": "Orocovis"
  },
  {
      "name": "Palmer",
    "state_code": "PR",
    "zip": "00721",
    "latitude": 18.3725,
    "longitude": -65.774444,
    "country": "Rio Grande"
  },
  {
      "name": "Patillas",
    "state_code": "PR",
    "zip": "00723",
    "latitude": 18.008333,
    "longitude": -66.013333,
    "country": "Patillas"
  },
  {
      "name": "Caguas",
    "state_code": "PR",
    "zip": "00725",
    "latitude": 18.2297,
    "longitude": -66.0458,
    "country": "Caguas"
  },
  {
      "name": "Canovanas",
    "state_code": "PR",
    "zip": "00729",
    "latitude": 18.379722,
    "longitude": -65.906667,
    "country": "Canovanas"
  },
  {
      "name": "Ceiba",
    "state_code": "PR",
    "zip": "00735",
    "latitude": 18.2531,
    "longitude": -65.656,
    "country": "Ceiba"
  },
  {
      "name": "Cayey",
    "state_code": "PR",
    "zip": "00736",
    "latitude": 18.1134,
    "longitude": -66.1521,
    "country": "Cayey"
  },
  {
      "name": "Fajardo",
    "state_code": "PR",
    "zip": "00738",
    "latitude": 18.330278,
    "longitude": -65.657222,
    "country": "Fajardo"
  },
  {
      "name": "Cidra",
    "state_code": "PR",
    "zip": "00739",
    "latitude": 18.176667,
    "longitude": -66.161667,
    "country": "Cidra"
  },
  {
      "name": "Puerto Real",
    "state_code": "PR",
    "zip": "00740",
    "latitude": 18.079167,
    "longitude": -67.185556,
    "country": "Fajardo"
  },
  {
      "name": "Punta Santiago",
    "state_code": "PR",
    "zip": "00741",
    "latitude": 18.168333,
    "longitude": -65.748611,
    "country": "Humacao"
  },
  {
      "name": "Roosevelt Roads",
    "state_code": "PR",
    "zip": "00742",
    "latitude": 18.2674,
    "longitude": -65.6483,
    "country": "Ceiba"
  },
  {
      "name": "Rio Blanco",
    "state_code": "PR",
    "zip": "00744",
    "latitude": 18.220278,
    "longitude": -65.788889,
    "country": "Naguabo"
  },
  {
      "name": "Rio Grande",
    "state_code": "PR",
    "zip": "00745",
    "latitude": 18.379444,
    "longitude": -65.838889,
    "country": "Rio Grande"
  },
  {
      "name": "Salinas",
    "state_code": "PR",
    "zip": "00751",
    "latitude": 17.979444,
    "longitude": -66.298333,
    "country": "Salinas"
  },
  {
      "name": "San Lorenzo",
    "state_code": "PR",
    "zip": "00754",
    "latitude": 18.191389,
    "longitude": -65.961389,
    "country": "San Lorenzo"
  },
  {
      "name": "Santa Isabel",
    "state_code": "PR",
    "zip": "00757",
    "latitude": 17.968056,
    "longitude": -66.405278,
    "country": "Santa Isabel"
  },
  {
      "name": "Vieques",
    "state_code": "PR",
    "zip": "00765",
    "latitude": 18.426389,
    "longitude": -65.835556,
    "country": "Vieques"
  },
  {
      "name": "Villalba",
    "state_code": "PR",
    "zip": "00766",
    "latitude": 18.129167,
    "longitude": -66.4925,
    "country": "Villalba"
  },
  {
      "name": "Yabucoa",
    "state_code": "PR",
    "zip": "00767",
    "latitude": 18.0525,
    "longitude": -65.879722,
    "country": "Yabucoa"
  },
  {
      "name": "Coamo",
    "state_code": "PR",
    "zip": "00769",
    "latitude": 18.081944,
    "longitude": -66.358333,
    "country": "Coamo"
  },
  {
      "name": "Las Piedras",
    "state_code": "PR",
    "zip": "00771",
    "latitude": 18.183333,
    "longitude": -65.863333,
    "country": "Las Piedras"
  },
  {
      "name": "Loiza",
    "state_code": "PR",
    "zip": "00772",
    "latitude": 18.433333,
    "longitude": -65.880556,
    "country": "Loiza"
  },
  {
      "name": "Luquillo",
    "state_code": "PR",
    "zip": "00773",
    "latitude": 18.374444,
    "longitude": -65.716944,
    "country": "Luquillo"
  },
  {
      "name": "Culebra",
    "state_code": "PR",
    "zip": "00775",
    "latitude": 18.305,
    "longitude": -65.301389,
    "country": "Culebra"
  },
  {
      "name": "Juncos",
    "state_code": "PR",
    "zip": "00777",
    "latitude": 18.229444,
    "longitude": -65.921389,
    "country": "Juncos"
  },
  {
      "name": "Gurabo",
    "state_code": "PR",
    "zip": "00778",
    "latitude": 18.255,
    "longitude": -65.9775,
    "country": "Gurabo"
  },
  {
      "name": "Coto Laurel",
    "state_code": "PR",
    "zip": "00780",
    "latitude": 18.0545,
    "longitude": -66.5625,
    "country": "Ponce"
  },
  {
      "name": "Comerio",
    "state_code": "PR",
    "zip": "00782",
    "latitude": 18.218333,
    "longitude": -66.225,
    "country": "Comerio"
  },
  {
      "name": "Corozal",
    "state_code": "PR",
    "zip": "00783",
    "latitude": 18.343056,
    "longitude": -66.317222,
    "country": "Corozal"
  },
  {
      "name": "Guayama",
    "state_code": "PR",
    "zip": "00784",
    "latitude": 17.9856,
    "longitude": -66.1307,
    "country": "Guayama"
  },
  {
      "name": "La Plata",
    "state_code": "PR",
    "zip": "00786",
    "latitude": 18.156389,
    "longitude": -66.233333,
    "country": "Aibonito"
  },
  {
      "name": "Humacao",
    "state_code": "PR",
    "zip": "00791",
    "latitude": 18.1415,
    "longitude": -65.8216,
    "country": "Humacao"
  },
  {
      "name": "Barranquitas",
    "state_code": "PR",
    "zip": "00794",
    "latitude": 18.188333,
    "longitude": -66.308333,
    "country": "Barranquitas"
  },
  {
      "name": "Juana Diaz",
    "state_code": "PR",
    "zip": "00795",
    "latitude": 18.054444,
    "longitude": -66.506944,
    "country": "Juana Diaz"
  },
  {
      "name": "San Juan",
    "state_code": "PR",
    "zip": "00901",
    "latitude": 18.4652,
    "longitude": -66.1071,
    "country": "San Juan"
  },
  {
      "name": "Fort Buchanan",
    "state_code": "PR",
    "zip": "00934",
    "latitude": 18.46,
    "longitude": -66.11,
    "country": "San Juan"
  },
  {
      "name": "Toa Baja",
    "state_code": "PR",
    "zip": "00949",
    "latitude": 18.451944,
    "longitude": -66.181944,
    "country": "Toa Baja"
  },
  {
      "name": "Sabana Seca",
    "state_code": "PR",
    "zip": "00952",
    "latitude": 18.428889,
    "longitude": -66.185,
    "country": "Toa Baja"
  },
  {
      "name": "Toa Alta",
    "state_code": "PR",
    "zip": "00953",
    "latitude": 18.3664,
    "longitude": -66.2333,
    "country": "Toa Alta"
  },
  {
      "name": "Bayamon",
    "state_code": "PR",
    "zip": "00956",
    "latitude": 18.3406,
    "longitude": -66.1663,
    "country": "Bayamon"
  },
  {
      "name": "Catano",
    "state_code": "PR",
    "zip": "00962",
    "latitude": 18.4375,
    "longitude": -66.141,
    "country": "Catano"
  },
  {
      "name": "Guaynabo",
    "state_code": "PR",
    "zip": "00965",
    "latitude": 18.4326,
    "longitude": -66.1173,
    "country": "Guaynabo"
  },
  {
      "name": "Trujillo Alto",
    "state_code": "PR",
    "zip": "00976",
    "latitude": 18.344,
    "longitude": -66.0053,
    "country": "Trujillo Alto"
  },
  {
      "name": "Saint Just",
    "state_code": "PR",
    "zip": "00978",
    "latitude": 18.37,
    "longitude": -66.012222,
    "country": "Trujillo Alto"
  },
  {
      "name": "Carolina",
    "state_code": "PR",
    "zip": "00979",
    "latitude": 18.4353,
    "longitude": -66.0143,
    "country": "Carolina"
  },
  {
      "name": "Agawam",
    "state_code": "MA",
    "zip": "01001",
    "latitude": 42.070206,
    "longitude": -72.622739,
    "country": "Hampden"
  },
  {
      "name": "Amherst",
    "state_code": "MA",
    "zip": "01002",
    "latitude": 42.377017,
    "longitude": -72.51565,
    "country": "Hampshire"
  },
  {
      "name": "Barre",
    "state_code": "MA",
    "zip": "01005",
    "latitude": 42.409698,
    "longitude": -72.108354,
    "country": "Worcester"
  },
  {
      "name": "Belchertown",
    "state_code": "MA",
    "zip": "01007",
    "latitude": 42.275103,
    "longitude": -72.410953,
    "country": "Hampshire"
  },
  {
      "name": "Blandford",
    "state_code": "MA",
    "zip": "01008",
    "latitude": 42.182949,
    "longitude": -72.936114,
    "country": "Hampden"
  },
  {
      "name": "Bondsville",
    "state_code": "MA",
    "zip": "01009",
    "latitude": 42.2125,
    "longitude": -72.345833,
    "country": "Hampden"
  },
  {
      "name": "Brimfield",
    "state_code": "MA",
    "zip": "01010",
    "latitude": 42.116543,
    "longitude": -72.188455,
    "country": "Hampden"
  },
  {
      "name": "Chester",
    "state_code": "MA",
    "zip": "01011",
    "latitude": 42.279421,
    "longitude": -72.988761,
    "country": "Hampden"
  },
  {
      "name": "Chesterfield",
    "state_code": "MA",
    "zip": "01012",
    "latitude": 42.38167,
    "longitude": -72.833309,
    "country": "Hampshire"
  },
  {
      "name": "Chicopee",
    "state_code": "MA",
    "zip": "01013",
    "latitude": 42.162046,
    "longitude": -72.607962,
    "country": "Hampden"
  },
  {
      "name": "Cummington",
    "state_code": "MA",
    "zip": "01026",
    "latitude": 42.435296,
    "longitude": -72.905767,
    "country": "Hampshire"
  },
  {
      "name": "Easthampton",
    "state_code": "MA",
    "zip": "01027",
    "latitude": 42.264319,
    "longitude": -72.679921,
    "country": "Hampshire"
  },
  {
      "name": "East Longmeadow",
    "state_code": "MA",
    "zip": "01028",
    "latitude": 42.067203,
    "longitude": -72.505565,
    "country": "Hampden"
  },
  {
      "name": "East Otis",
    "state_code": "MA",
    "zip": "01029",
    "latitude": 42.173333,
    "longitude": -73.035,
    "country": "Berkshire"
  },
  {
      "name": "Feeding Hills",
    "state_code": "MA",
    "zip": "01030",
    "latitude": 42.07182,
    "longitude": -72.675077,
    "country": "Hampden"
  },
  {
      "name": "Gilbertville",
    "state_code": "MA",
    "zip": "01031",
    "latitude": 42.332194,
    "longitude": -72.198585,
    "country": "Worcester"
  },
  {
      "name": "Goshen",
    "state_code": "MA",
    "zip": "01032",
    "latitude": 42.466234,
    "longitude": -72.844092,
    "country": "Hampshire"
  },
  {
      "name": "Granby",
    "state_code": "MA",
    "zip": "01033",
    "latitude": 42.255704,
    "longitude": -72.520001,
    "country": "Hampshire"
  },
  {
      "name": "Granville",
    "state_code": "MA",
    "zip": "01034",
    "latitude": 42.070234,
    "longitude": -72.908793,
    "country": "Hampden"
  },
  {
      "name": "Hadley",
    "state_code": "MA",
    "zip": "01035",
    "latitude": 42.36062,
    "longitude": -72.571499,
    "country": "Hampshire"
  },
  {
      "name": "Hampden",
    "state_code": "MA",
    "zip": "01036",
    "latitude": 42.064756,
    "longitude": -72.431823,
    "country": "Hampden"
  },
  {
      "name": "Hardwick",
    "state_code": "MA",
    "zip": "01037",
    "latitude": 42.35,
    "longitude": -72.2,
    "country": "Worcester"
  },
  {
      "name": "Hatfield",
    "state_code": "MA",
    "zip": "01038",
    "latitude": 42.38439,
    "longitude": -72.616735,
    "country": "Hampshire"
  },
  {
      "name": "Haydenville",
    "state_code": "MA",
    "zip": "01039",
    "latitude": 42.381799,
    "longitude": -72.703178,
    "country": "Hampshire"
  },
  {
      "name": "Holyoke",
    "state_code": "MA",
    "zip": "01040",
    "latitude": 42.202007,
    "longitude": -72.626193,
    "country": "Hampden"
  },
  {
      "name": "Huntington",
    "state_code": "MA",
    "zip": "01050",
    "latitude": 42.265301,
    "longitude": -72.873341,
    "country": "Hampshire"
  },
  {
      "name": "Leeds",
    "state_code": "MA",
    "zip": "01053",
    "latitude": 42.354292,
    "longitude": -72.703403,
    "country": "Hampshire"
  },
  {
      "name": "Leverett",
    "state_code": "MA",
    "zip": "01054",
    "latitude": 42.46823,
    "longitude": -72.499334,
    "country": "Franklin"
  },
  {
      "name": "Ludlow",
    "state_code": "MA",
    "zip": "01056",
    "latitude": 42.172823,
    "longitude": -72.471012,
    "country": "Hampden"
  },
  {
      "name": "Monson",
    "state_code": "MA",
    "zip": "01057",
    "latitude": 42.101017,
    "longitude": -72.319634,
    "country": "Hampden"
  },
  {
      "name": "North Amherst",
    "state_code": "MA",
    "zip": "01059",
    "latitude": 42.4102,
    "longitude": -72.5313,
    "country": "Hampshire"
  },
  {
      "name": "Northampton",
    "state_code": "MA",
    "zip": "01060",
    "latitude": 42.324662,
    "longitude": -72.654245,
    "country": "Hampshire"
  },
  {
      "name": "North Hatfield",
    "state_code": "MA",
    "zip": "01066",
    "latitude": 42.411111,
    "longitude": -72.622778,
    "country": "Hampshire"
  },
  {
      "name": "Oakham",
    "state_code": "MA",
    "zip": "01068",
    "latitude": 42.348033,
    "longitude": -72.051265,
    "country": "Worcester"
  },
  {
      "name": "Palmer",
    "state_code": "MA",
    "zip": "01069",
    "latitude": 42.176233,
    "longitude": -72.328785,
    "country": "Hampden"
  },
  {
      "name": "Plainfield",
    "state_code": "MA",
    "zip": "01070",
    "latitude": 42.514393,
    "longitude": -72.918289,
    "country": "Hampshire"
  },
  {
      "name": "Russell",
    "state_code": "MA",
    "zip": "01071",
    "latitude": 42.147063,
    "longitude": -72.840343,
    "country": "Hampden"
  },
  {
      "name": "Shutesbury",
    "state_code": "MA",
    "zip": "01072",
    "latitude": 42.481968,
    "longitude": -72.421342,
    "country": "Franklin"
  },
  {
      "name": "Southampton",
    "state_code": "MA",
    "zip": "01073",
    "latitude": 42.224697,
    "longitude": -72.719381,
    "country": "Hampshire"
  },
  {
      "name": "South Barre",
    "state_code": "MA",
    "zip": "01074",
    "latitude": 42.385278,
    "longitude": -72.095833,
    "country": "Worcester"
  },
  {
      "name": "South Hadley",
    "state_code": "MA",
    "zip": "01075",
    "latitude": 42.237537,
    "longitude": -72.581137,
    "country": "Hampshire"
  },
  {
      "name": "Southwick",
    "state_code": "MA",
    "zip": "01077",
    "latitude": 42.051099,
    "longitude": -72.770588,
    "country": "Hampden"
  },
  {
      "name": "Thorndike",
    "state_code": "MA",
    "zip": "01079",
    "latitude": 42.1875,
    "longitude": -72.336111,
    "country": "Hampden"
  },
  {
      "name": "Three Rivers",
    "state_code": "MA",
    "zip": "01080",
    "latitude": 42.181894,
    "longitude": -72.362352,
    "country": "Hampden"
  },
  {
      "name": "Wales",
    "state_code": "MA",
    "zip": "01081",
    "latitude": 42.062734,
    "longitude": -72.204592,
    "country": "Hampden"
  },
  {
      "name": "Ware",
    "state_code": "MA",
    "zip": "01082",
    "latitude": 42.261831,
    "longitude": -72.258285,
    "country": "Hampshire"
  },
  {
      "name": "Warren",
    "state_code": "MA",
    "zip": "01083",
    "latitude": 42.2125,
    "longitude": -72.191667,
    "country": "Worcester"
  },
  {
      "name": "West Chesterfield",
    "state_code": "MA",
    "zip": "01084",
    "latitude": 42.402778,
    "longitude": -72.876389,
    "country": "Hampshire"
  },
  {
      "name": "Westfield",
    "state_code": "MA",
    "zip": "01085",
    "latitude": 42.129484,
    "longitude": -72.754318,
    "country": "Hampden"
  },
  {
      "name": "West Hatfield",
    "state_code": "MA",
    "zip": "01088",
    "latitude": 42.370833,
    "longitude": -72.6375,
    "country": "Hampshire"
  },
  {
      "name": "West Springfield",
    "state_code": "MA",
    "zip": "01089",
    "latitude": 42.115066,
    "longitude": -72.641109,
    "country": "Hampden"
  },
  {
      "name": "West Warren",
    "state_code": "MA",
    "zip": "01092",
    "latitude": 42.20734,
    "longitude": -72.203639,
    "country": "Worcester"
  },
  {
      "name": "Whately",
    "state_code": "MA",
    "zip": "01093",
    "latitude": 42.439722,
    "longitude": -72.635278,
    "country": "Franklin"
  },
  {
      "name": "Wheelwright",
    "state_code": "MA",
    "zip": "01094",
    "latitude": 42.351944,
    "longitude": -72.140278,
    "country": "Worcester"
  },
  {
      "name": "Wilbraham",
    "state_code": "MA",
    "zip": "01095",
    "latitude": 42.124506,
    "longitude": -72.446415,
    "country": "Hampden"
  },
  {
      "name": "Williamsburg",
    "state_code": "MA",
    "zip": "01096",
    "latitude": 42.408522,
    "longitude": -72.777989,
    "country": "Hampshire"
  },
  {
      "name": "Woronoco",
    "state_code": "MA",
    "zip": "01097",
    "latitude": 42.163889,
    "longitude": -72.83,
    "country": "Hampden"
  },
  {
      "name": "Worthington",
    "state_code": "MA",
    "zip": "01098",
    "latitude": 42.384293,
    "longitude": -72.931427,
    "country": "Hampshire"
  },
  {
      "name": "Springfield",
    "state_code": "MA",
    "zip": "01101",
    "latitude": 42.106,
    "longitude": -72.5977,
    "country": "Hampden"
  },
  {
      "name": "Longmeadow",
    "state_code": "MA",
    "zip": "01106",
    "latitude": 42.050658,
    "longitude": -72.5676,
    "country": "Hampden"
  },
  {
      "name": "Pittsfield",
    "state_code": "MA",
    "zip": "01201",
    "latitude": 42.453086,
    "longitude": -73.247088,
    "country": "Berkshire"
  },
  {
      "name": "Adams",
    "state_code": "MA",
    "zip": "01220",
    "latitude": 42.622319,
    "longitude": -73.117225,
    "country": "Berkshire"
  },
  {
      "name": "Ashley Falls",
    "state_code": "MA",
    "zip": "01222",
    "latitude": 42.059552,
    "longitude": -73.320195,
    "country": "Berkshire"
  },
  {
      "name": "Becket",
    "state_code": "MA",
    "zip": "01223",
    "latitude": 42.359363,
    "longitude": -73.120325,
    "country": "Berkshire"
  },
  {
      "name": "Berkshire",
    "state_code": "MA",
    "zip": "01224",
    "latitude": 42.5125,
    "longitude": -73.193333,
    "country": "Berkshire"
  },
  {
      "name": "Cheshire",
    "state_code": "MA",
    "zip": "01225",
    "latitude": 42.561059,
    "longitude": -73.157964,
    "country": "Berkshire"
  },
  {
      "name": "Dalton",
    "state_code": "MA",
    "zip": "01226",
    "latitude": 42.475046,
    "longitude": -73.160259,
    "country": "Berkshire"
  },
  {
      "name": "Glendale",
    "state_code": "MA",
    "zip": "01229",
    "latitude": 42.283333,
    "longitude": -73.344444,
    "country": "Berkshire"
  },
  {
      "name": "Great Barrington",
    "state_code": "MA",
    "zip": "01230",
    "latitude": 42.195922,
    "longitude": -73.36065,
    "country": "Berkshire"
  },
  {
      "name": "Hinsdale",
    "state_code": "MA",
    "zip": "01235",
    "latitude": 42.434604,
    "longitude": -73.092433,
    "country": "Berkshire"
  },
  {
      "name": "Housatonic",
    "state_code": "MA",
    "zip": "01236",
    "latitude": 42.265296,
    "longitude": -73.374544,
    "country": "Berkshire"
  },
  {
      "name": "Lanesboro",
    "state_code": "MA",
    "zip": "01237",
    "latitude": 42.541961,
    "longitude": -73.248737,
    "country": "Berkshire"
  },
  {
      "name": "Lee",
    "state_code": "MA",
    "zip": "01238",
    "latitude": 42.298994,
    "longitude": -73.231696,
    "country": "Berkshire"
  },
  {
      "name": "Lenox",
    "state_code": "MA",
    "zip": "01240",
    "latitude": 42.364241,
    "longitude": -73.271322,
    "country": "Berkshire"
  },
  {
      "name": "Lenox Dale",
    "state_code": "MA",
    "zip": "01242",
    "latitude": 42.336111,
    "longitude": -73.245833,
    "country": "Berkshire"
  },
  {
      "name": "Middlefield",
    "state_code": "MA",
    "zip": "01243",
    "latitude": 42.34795,
    "longitude": -73.006226,
    "country": "Hampshire"
  },
  {
      "name": "Mill River",
    "state_code": "MA",
    "zip": "01244",
    "latitude": 42.113889,
    "longitude": -73.268056,
    "country": "Berkshire"
  },
  {
      "name": "Monterey",
    "state_code": "MA",
    "zip": "01245",
    "latitude": 42.187847,
    "longitude": -73.213452,
    "country": "Berkshire"
  },
  {
      "name": "North Adams",
    "state_code": "MA",
    "zip": "01247",
    "latitude": 42.69865,
    "longitude": -73.10999,
    "country": "Berkshire"
  },
  {
      "name": "North Egremont",
    "state_code": "MA",
    "zip": "01252",
    "latitude": 42.196667,
    "longitude": -73.438333,
    "country": "Berkshire"
  },
  {
      "name": "Otis",
    "state_code": "MA",
    "zip": "01253",
    "latitude": 42.18988,
    "longitude": -73.082093,
    "country": "Berkshire"
  },
  {
      "name": "Richmond",
    "state_code": "MA",
    "zip": "01254",
    "latitude": 42.378398,
    "longitude": -73.364457,
    "country": "Berkshire"
  },
  {
      "name": "Sandisfield",
    "state_code": "MA",
    "zip": "01255",
    "latitude": 42.109429,
    "longitude": -73.116285,
    "country": "Berkshire"
  },
  {
      "name": "Savoy",
    "state_code": "MA",
    "zip": "01256",
    "latitude": 42.576964,
    "longitude": -73.023281,
    "country": "Berkshire"
  },
  {
      "name": "Sheffield",
    "state_code": "MA",
    "zip": "01257",
    "latitude": 42.100102,
    "longitude": -73.361091,
    "country": "Berkshire"
  },
  {
      "name": "South Egremont",
    "state_code": "MA",
    "zip": "01258",
    "latitude": 42.101153,
    "longitude": -73.456575,
    "country": "Berkshire"
  },
  {
      "name": "Southfield",
    "state_code": "MA",
    "zip": "01259",
    "latitude": 42.078014,
    "longitude": -73.260933,
    "country": "Berkshire"
  },
  {
      "name": "South Lee",
    "state_code": "MA",
    "zip": "01260",
    "latitude": 42.277778,
    "longitude": -73.277778,
    "country": "Berkshire"
  },
  {
      "name": "Stockbridge",
    "state_code": "MA",
    "zip": "01262",
    "latitude": 42.30104,
    "longitude": -73.322263,
    "country": "Berkshire"
  },
  {
      "name": "Tyringham",
    "state_code": "MA",
    "zip": "01264",
    "latitude": 42.245833,
    "longitude": -73.204167,
    "country": "Berkshire"
  },
  {
      "name": "West Stockbridge",
    "state_code": "MA",
    "zip": "01266",
    "latitude": 42.334752,
    "longitude": -73.38251,
    "country": "Berkshire"
  },
  {
      "name": "Williamstown",
    "state_code": "MA",
    "zip": "01267",
    "latitude": 42.708883,
    "longitude": -73.20364,
    "country": "Berkshire"
  },
  {
      "name": "Windsor",
    "state_code": "MA",
    "zip": "01270",
    "latitude": 42.509494,
    "longitude": -73.04661,
    "country": "Berkshire"
  },
  {
      "name": "Greenfield",
    "state_code": "MA",
    "zip": "01301",
    "latitude": 42.601222,
    "longitude": -72.601847,
    "country": "Franklin"
  },
  {
      "name": "Ashfield",
    "state_code": "MA",
    "zip": "01330",
    "latitude": 42.523207,
    "longitude": -72.810998,
    "country": "Franklin"
  },
  {
      "name": "Athol",
    "state_code": "MA",
    "zip": "01331",
    "latitude": 42.592065,
    "longitude": -72.214644,
    "country": "Worcester"
  },
  {
      "name": "Bernardston",
    "state_code": "MA",
    "zip": "01337",
    "latitude": 42.683784,
    "longitude": -72.563439,
    "country": "Franklin"
  },
  {
      "name": "Buckland",
    "state_code": "MA",
    "zip": "01338",
    "latitude": 42.615174,
    "longitude": -72.764124,
    "country": "Franklin"
  },
  {
      "name": "Charlemont",
    "state_code": "MA",
    "zip": "01339",
    "latitude": 42.621802,
    "longitude": -72.880162,
    "country": "Franklin"
  },
  {
      "name": "Colrain",
    "state_code": "MA",
    "zip": "01340",
    "latitude": 42.67905,
    "longitude": -72.726508,
    "country": "Franklin"
  },
  {
      "name": "Conway",
    "state_code": "MA",
    "zip": "01341",
    "latitude": 42.513832,
    "longitude": -72.702473,
    "country": "Franklin"
  },
  {
      "name": "Deerfield",
    "state_code": "MA",
    "zip": "01342",
    "latitude": 42.540636,
    "longitude": -72.607234,
    "country": "Franklin"
  },
  {
      "name": "Drury",
    "state_code": "MA",
    "zip": "01343",
    "latitude": 42.652222,
    "longitude": -72.998056,
    "country": "Berkshire"
  },
  {
      "name": "Erving",
    "state_code": "MA",
    "zip": "01344",
    "latitude": 42.604957,
    "longitude": -72.416638,
    "country": "Franklin"
  },
  {
      "name": "Heath",
    "state_code": "MA",
    "zip": "01346",
    "latitude": 42.685347,
    "longitude": -72.839101,
    "country": "Franklin"
  },
  {
      "name": "Lake Pleasant",
    "state_code": "MA",
    "zip": "01347",
    "latitude": 42.556389,
    "longitude": -72.518611,
    "country": "Franklin"
  },
  {
      "name": "Millers Falls",
    "state_code": "MA",
    "zip": "01349",
    "latitude": 42.576206,
    "longitude": -72.494626,
    "country": "Franklin"
  },
  {
      "name": "Monroe Bridge",
    "state_code": "MA",
    "zip": "01350",
    "latitude": 42.723885,
    "longitude": -72.960156,
    "country": "Franklin"
  },
  {
      "name": "Montague",
    "state_code": "MA",
    "zip": "01351",
    "latitude": 42.542864,
    "longitude": -72.532837,
    "country": "Franklin"
  },
  {
      "name": "Gill",
    "state_code": "MA",
    "zip": "01354",
    "latitude": 42.676944,
    "longitude": -72.447222,
    "country": "Franklin"
  },
  {
      "name": "New Salem",
    "state_code": "MA",
    "zip": "01355",
    "latitude": 42.514643,
    "longitude": -72.306241,
    "country": "Franklin"
  },
  {
      "name": "Northfield",
    "state_code": "MA",
    "zip": "01360",
    "latitude": 42.688705,
    "longitude": -72.450995,
    "country": "Franklin"
  },
  {
      "name": "Orange",
    "state_code": "MA",
    "zip": "01364",
    "latitude": 42.591231,
    "longitude": -72.305867,
    "country": "Franklin"
  },
  {
      "name": "Petersham",
    "state_code": "MA",
    "zip": "01366",
    "latitude": 42.489761,
    "longitude": -72.189349,
    "country": "Worcester"
  },
  {
      "name": "Rowe",
    "state_code": "MA",
    "zip": "01367",
    "latitude": 42.695289,
    "longitude": -72.925776,
    "country": "Franklin"
  },
  {
      "name": "Royalston",
    "state_code": "MA",
    "zip": "01368",
    "latitude": 42.5925,
    "longitude": -72.227778,
    "country": "Worcester"
  },
  {
      "name": "Shelburne Falls",
    "state_code": "MA",
    "zip": "01370",
    "latitude": 42.602203,
    "longitude": -72.739059,
    "country": "Franklin"
  },
  {
      "name": "South Deerfield",
    "state_code": "MA",
    "zip": "01373",
    "latitude": 42.475616,
    "longitude": -72.615268,
    "country": "Franklin"
  },
  {
      "name": "Sunderland",
    "state_code": "MA",
    "zip": "01375",
    "latitude": 42.453947,
    "longitude": -72.567569,
    "country": "Franklin"
  },
  {
      "name": "Turners Falls",
    "state_code": "MA",
    "zip": "01376",
    "latitude": 42.606521,
    "longitude": -72.54701,
    "country": "Franklin"
  },
  {
      "name": "Warwick",
    "state_code": "MA",
    "zip": "01378",
    "latitude": 42.681944,
    "longitude": -72.339444,
    "country": "Franklin"
  },
  {
      "name": "Wendell",
    "state_code": "MA",
    "zip": "01379",
    "latitude": 42.565644,
    "longitude": -72.400851,
    "country": "Franklin"
  },
  {
      "name": "Wendell Depot",
    "state_code": "MA",
    "zip": "01380",
    "latitude": 42.595833,
    "longitude": -72.360278,
    "country": "Franklin"
  },
  {
      "name": "Fitchburg",
    "state_code": "MA",
    "zip": "01420",
    "latitude": 42.579563,
    "longitude": -71.803133,
    "country": "Worcester"
  },
  {
      "name": "Ashburnham",
    "state_code": "MA",
    "zip": "01430",
    "latitude": 42.649614,
    "longitude": -71.92666,
    "country": "Worcester"
  },
  {
      "name": "Ashby",
    "state_code": "MA",
    "zip": "01431",
    "latitude": 42.674462,
    "longitude": -71.817369,
    "country": "Middlesex"
  },
  {
      "name": "Ayer",
    "state_code": "MA",
    "zip": "01432",
    "latitude": 42.55914,
    "longitude": -71.578763,
    "country": "Middlesex"
  },
  {
      "name": "Devens",
    "state_code": "MA",
    "zip": "01434",
    "latitude": 42.546667,
    "longitude": -71.598333,
    "country": "Middlesex"
  },
  {
      "name": "Baldwinville",
    "state_code": "MA",
    "zip": "01436",
    "latitude": 42.593568,
    "longitude": -72.064647,
    "country": "Worcester"
  },
  {
      "name": "East Templeton",
    "state_code": "MA",
    "zip": "01438",
    "latitude": 42.563056,
    "longitude": -72.0375,
    "country": "Worcester"
  },
  {
      "name": "Gardner",
    "state_code": "MA",
    "zip": "01440",
    "latitude": 42.57405,
    "longitude": -71.9898,
    "country": "Worcester"
  },
  {
      "name": "Westminster",
    "state_code": "MA",
    "zip": "01441",
    "latitude": 42.545833,
    "longitude": -71.911111,
    "country": "Worcester"
  },
  {
      "name": "Groton",
    "state_code": "MA",
    "zip": "01450",
    "latitude": 42.612351,
    "longitude": -71.558371,
    "country": "Middlesex"
  },
  {
      "name": "Harvard",
    "state_code": "MA",
    "zip": "01451",
    "latitude": 42.498565,
    "longitude": -71.575293,
    "country": "Worcester"
  },
  {
      "name": "Hubbardston",
    "state_code": "MA",
    "zip": "01452",
    "latitude": 42.486538,
    "longitude": -72.001159,
    "country": "Worcester"
  },
  {
      "name": "Leominster",
    "state_code": "MA",
    "zip": "01453",
    "latitude": 42.52744,
    "longitude": -71.756308,
    "country": "Worcester"
  },
  {
      "name": "Littleton",
    "state_code": "MA",
    "zip": "01460",
    "latitude": 42.540132,
    "longitude": -71.487667,
    "country": "Middlesex"
  },
  {
      "name": "Lunenburg",
    "state_code": "MA",
    "zip": "01462",
    "latitude": 42.58843,
    "longitude": -71.726642,
    "country": "Worcester"
  },
  {
      "name": "Pepperell",
    "state_code": "MA",
    "zip": "01463",
    "latitude": 42.668888,
    "longitude": -71.593392,
    "country": "Middlesex"
  },
  {
      "name": "Shirley",
    "state_code": "MA",
    "zip": "01464",
    "latitude": 42.558653,
    "longitude": -71.646444,
    "country": "Middlesex"
  },
  {
      "name": "Still River",
    "state_code": "MA",
    "zip": "01467",
    "latitude": 42.491667,
    "longitude": -71.618056,
    "country": "Worcester"
  },
  {
      "name": "Templeton",
    "state_code": "MA",
    "zip": "01468",
    "latitude": 42.545976,
    "longitude": -72.064971,
    "country": "Worcester"
  },
  {
      "name": "Townsend",
    "state_code": "MA",
    "zip": "01469",
    "latitude": 42.652511,
    "longitude": -71.689646,
    "country": "Middlesex"
  },
  {
      "name": "West Groton",
    "state_code": "MA",
    "zip": "01472",
    "latitude": 42.604167,
    "longitude": -71.627778,
    "country": "Middlesex"
  },
  {
      "name": "West Townsend",
    "state_code": "MA",
    "zip": "01474",
    "latitude": 42.670404,
    "longitude": -71.74057,
    "country": "Middlesex"
  },
  {
      "name": "Winchendon",
    "state_code": "MA",
    "zip": "01475",
    "latitude": 42.678943,
    "longitude": -72.047524,
    "country": "Worcester"
  },
  {
      "name": "Winchendon Springs",
    "state_code": "MA",
    "zip": "01477",
    "latitude": 42.694444,
    "longitude": -72.015278,
    "country": "Worcester"
  },
  {
      "name": "Auburn",
    "state_code": "MA",
    "zip": "01501",
    "latitude": 42.205502,
    "longitude": -71.839144,
    "country": "Worcester"
  },
  {
      "name": "Berlin",
    "state_code": "MA",
    "zip": "01503",
    "latitude": 42.384438,
    "longitude": -71.635634,
    "country": "Worcester"
  },
  {
      "name": "Blackstone",
    "state_code": "MA",
    "zip": "01504",
    "latitude": 42.028708,
    "longitude": -71.52691,
    "country": "Worcester"
  },
  {
      "name": "Boylston",
    "state_code": "MA",
    "zip": "01505",
    "latitude": 42.337727,
    "longitude": -71.731042,
    "country": "Worcester"
  },
  {
      "name": "Brookfield",
    "state_code": "MA",
    "zip": "01506",
    "latitude": 42.199141,
    "longitude": -72.098887,
    "country": "Worcester"
  },
  {
      "name": "Charlton",
    "state_code": "MA",
    "zip": "01507",
    "latitude": 42.137902,
    "longitude": -71.966384,
    "country": "Worcester"
  },
  {
      "name": "Charlton City",
    "state_code": "MA",
    "zip": "01508",
    "latitude": 42.145833,
    "longitude": -71.988889,
    "country": "Worcester"
  },
  {
      "name": "Charlton Depot",
    "state_code": "MA",
    "zip": "01509",
    "latitude": 42.173056,
    "longitude": -71.979167,
    "country": "Worcester"
  },
  {
      "name": "Clinton",
    "state_code": "MA",
    "zip": "01510",
    "latitude": 42.418147,
    "longitude": -71.682847,
    "country": "Worcester"
  },
  {
      "name": "East Brookfield",
    "state_code": "MA",
    "zip": "01515",
    "latitude": 42.219308,
    "longitude": -72.048078,
    "country": "Worcester"
  },
  {
      "name": "Douglas",
    "state_code": "MA",
    "zip": "01516",
    "latitude": 42.060566,
    "longitude": -71.726611,
    "country": "Worcester"
  },
  {
      "name": "East Princeton",
    "state_code": "MA",
    "zip": "01517",
    "latitude": 42.472778,
    "longitude": -71.839444,
    "country": "Worcester"
  },
  {
      "name": "Fiskdale",
    "state_code": "MA",
    "zip": "01518",
    "latitude": 42.122762,
    "longitude": -72.117764,
    "country": "Worcester"
  },
  {
      "name": "Grafton",
    "state_code": "MA",
    "zip": "01519",
    "latitude": 42.200371,
    "longitude": -71.686848,
    "country": "Worcester"
  },
  {
      "name": "Holden",
    "state_code": "MA",
    "zip": "01520",
    "latitude": 42.341983,
    "longitude": -71.84142,
    "country": "Worcester"
  },
  {
      "name": "Holland",
    "state_code": "MA",
    "zip": "01521",
    "latitude": 42.040264,
    "longitude": -72.154373,
    "country": "Hampden"
  },
  {
      "name": "Jefferson",
    "state_code": "MA",
    "zip": "01522",
    "latitude": 42.375519,
    "longitude": -71.87058,
    "country": "Worcester"
  },
  {
      "name": "Lancaster",
    "state_code": "MA",
    "zip": "01523",
    "latitude": 42.450984,
    "longitude": -71.686831,
    "country": "Worcester"
  },
  {
      "name": "Leicester",
    "state_code": "MA",
    "zip": "01524",
    "latitude": 42.237047,
    "longitude": -71.918829,
    "country": "Worcester"
  },
  {
      "name": "Linwood",
    "state_code": "MA",
    "zip": "01525",
    "latitude": 42.097222,
    "longitude": -71.645278,
    "country": "Worcester"
  },
  {
      "name": "Manchaug",
    "state_code": "MA",
    "zip": "01526",
    "latitude": 42.094444,
    "longitude": -71.748056,
    "country": "Worcester"
  },
  {
      "name": "Millbury",
    "state_code": "MA",
    "zip": "01527",
    "latitude": 42.196779,
    "longitude": -71.764438,
    "country": "Worcester"
  },
  {
      "name": "Millville",
    "state_code": "MA",
    "zip": "01529",
    "latitude": 42.033102,
    "longitude": -71.579813,
    "country": "Worcester"
  },
  {
      "name": "New Braintree",
    "state_code": "MA",
    "zip": "01531",
    "latitude": 42.31977,
    "longitude": -72.130642,
    "country": "Worcester"
  },
  {
      "name": "Northborough",
    "state_code": "MA",
    "zip": "01532",
    "latitude": 42.318242,
    "longitude": -71.646372,
    "country": "Worcester"
  },
  {
      "name": "Northbridge",
    "state_code": "MA",
    "zip": "01534",
    "latitude": 42.1494,
    "longitude": -71.656366,
    "country": "Worcester"
  },
  {
      "name": "North Brookfield",
    "state_code": "MA",
    "zip": "01535",
    "latitude": 42.266455,
    "longitude": -72.082129,
    "country": "Worcester"
  },
  {
      "name": "North Grafton",
    "state_code": "MA",
    "zip": "01536",
    "latitude": 42.229726,
    "longitude": -71.703691,
    "country": "Worcester"
  },
  {
      "name": "North Oxford",
    "state_code": "MA",
    "zip": "01537",
    "latitude": 42.16549,
    "longitude": -71.885953,
    "country": "Worcester"
  },
  {
      "name": "North Uxbridge",
    "state_code": "MA",
    "zip": "01538",
    "latitude": 42.0875,
    "longitude": -71.641667,
    "country": "Worcester"
  },
  {
      "name": "Oxford",
    "state_code": "MA",
    "zip": "01540",
    "latitude": 42.11285,
    "longitude": -71.868677,
    "country": "Worcester"
  },
  {
      "name": "Princeton",
    "state_code": "MA",
    "zip": "01541",
    "latitude": 42.450812,
    "longitude": -71.876245,
    "country": "Worcester"
  },
  {
      "name": "Rochdale",
    "state_code": "MA",
    "zip": "01542",
    "latitude": 42.199685,
    "longitude": -71.906882,
    "country": "Worcester"
  },
  {
      "name": "Rutland",
    "state_code": "MA",
    "zip": "01543",
    "latitude": 42.376199,
    "longitude": -71.948951,
    "country": "Worcester"
  },
  {
      "name": "Shrewsbury",
    "state_code": "MA",
    "zip": "01545",
    "latitude": 42.284801,
    "longitude": -71.720503,
    "country": "Worcester"
  },
  {
      "name": "Southbridge",
    "state_code": "MA",
    "zip": "01550",
    "latitude": 42.075024,
    "longitude": -72.035347,
    "country": "Worcester"
  },
  {
      "name": "South Grafton",
    "state_code": "MA",
    "zip": "01560",
    "latitude": 42.176042,
    "longitude": -71.692725,
    "country": "Worcester"
  },
  {
      "name": "South Lancaster",
    "state_code": "MA",
    "zip": "01561",
    "latitude": 42.444444,
    "longitude": -71.6875,
    "country": "Worcester"
  },
  {
      "name": "Spencer",
    "state_code": "MA",
    "zip": "01562",
    "latitude": 42.244103,
    "longitude": -71.990617,
    "country": "Worcester"
  },
  {
      "name": "Sterling",
    "state_code": "MA",
    "zip": "01564",
    "latitude": 42.435351,
    "longitude": -71.775192,
    "country": "Worcester"
  },
  {
      "name": "Sturbridge",
    "state_code": "MA",
    "zip": "01566",
    "latitude": 42.112619,
    "longitude": -72.084233,
    "country": "Worcester"
  },
  {
      "name": "Upton",
    "state_code": "MA",
    "zip": "01568",
    "latitude": 42.173275,
    "longitude": -71.608014,
    "country": "Worcester"
  },
  {
      "name": "Uxbridge",
    "state_code": "MA",
    "zip": "01569",
    "latitude": 42.074426,
    "longitude": -71.632869,
    "country": "Worcester"
  },
  {
      "name": "Webster",
    "state_code": "MA",
    "zip": "01570",
    "latitude": 42.047574,
    "longitude": -71.839467,
    "country": "Worcester"
  },
  {
      "name": "Dudley",
    "state_code": "MA",
    "zip": "01571",
    "latitude": 42.048894,
    "longitude": -71.893228,
    "country": "Worcester"
  },
  {
      "name": "Westborough",
    "state_code": "MA",
    "zip": "01580",
    "latitude": 42.283,
    "longitude": -71.6015,
    "country": "Worcester"
  },
  {
      "name": "West Boylston",
    "state_code": "MA",
    "zip": "01583",
    "latitude": 42.35836,
    "longitude": -71.783822,
    "country": "Worcester"
  },
  {
      "name": "West Brookfield",
    "state_code": "MA",
    "zip": "01585",
    "latitude": 42.244137,
    "longitude": -72.151137,
    "country": "Worcester"
  },
  {
      "name": "West Millbury",
    "state_code": "MA",
    "zip": "01586",
    "latitude": 42.171111,
    "longitude": -71.804167,
    "country": "Worcester"
  },
  {
      "name": "Whitinsville",
    "state_code": "MA",
    "zip": "01588",
    "latitude": 42.115319,
    "longitude": -71.664357,
    "country": "Worcester"
  },
  {
      "name": "Sutton",
    "state_code": "MA",
    "zip": "01590",
    "latitude": 42.140586,
    "longitude": -71.748416,
    "country": "Worcester"
  },
  {
      "name": "Worcester",
    "state_code": "MA",
    "zip": "01601",
    "latitude": 42.2621,
    "longitude": -71.8034,
    "country": "Worcester"
  },
  {
      "name": "Cherry Valley",
    "state_code": "MA",
    "zip": "01611",
    "latitude": 42.237287,
    "longitude": -71.874971,
    "country": "Worcester"
  },
  {
      "name": "Paxton",
    "state_code": "MA",
    "zip": "01612",
    "latitude": 42.306646,
    "longitude": -71.920234,
    "country": "Worcester"
  },
  {
      "name": "Framingham",
    "state_code": "MA",
    "zip": "01701",
    "latitude": 42.300665,
    "longitude": -71.425486,
    "country": "Middlesex"
  },
  {
      "name": "Village Of Nagog Woods",
    "state_code": "MA",
    "zip": "01718",
    "latitude": 42.514941,
    "longitude": -71.422354,
    "country": "Middlesex"
  },
  {
      "name": "Boxborough",
    "state_code": "MA",
    "zip": "01719",
    "latitude": 42.486876,
    "longitude": -71.518229,
    "country": "Middlesex"
  },
  {
      "name": "Acton",
    "state_code": "MA",
    "zip": "01720",
    "latitude": 42.475076,
    "longitude": -71.448255,
    "country": "Middlesex"
  },
  {
      "name": "Ashland",
    "state_code": "MA",
    "zip": "01721",
    "latitude": 42.253909,
    "longitude": -71.458347,
    "country": "Middlesex"
  },
  {
      "name": "Bedford",
    "state_code": "MA",
    "zip": "01730",
    "latitude": 42.484287,
    "longitude": -71.276796,
    "country": "Middlesex"
  },
  {
      "name": "Hanscom Afb",
    "state_code": "MA",
    "zip": "01731",
    "latitude": 42.464444,
    "longitude": -71.284167,
    "country": "Middlesex"
  },
  {
      "name": "Bolton",
    "state_code": "MA",
    "zip": "01740",
    "latitude": 42.436523,
    "longitude": -71.607593,
    "country": "Worcester"
  },
  {
      "name": "Carlisle",
    "state_code": "MA",
    "zip": "01741",
    "latitude": 42.528562,
    "longitude": -71.351892,
    "country": "Middlesex"
  },
  {
      "name": "Concord",
    "state_code": "MA",
    "zip": "01742",
    "latitude": 42.456701,
    "longitude": -71.374741,
    "country": "Middlesex"
  },
  {
      "name": "Fayville",
    "state_code": "MA",
    "zip": "01745",
    "latitude": 42.293221,
    "longitude": -71.502256,
    "country": "Worcester"
  },
  {
      "name": "Holliston",
    "state_code": "MA",
    "zip": "01746",
    "latitude": 42.202641,
    "longitude": -71.436059,
    "country": "Middlesex"
  },
  {
      "name": "Hopedale",
    "state_code": "MA",
    "zip": "01747",
    "latitude": 42.126796,
    "longitude": -71.537601,
    "country": "Worcester"
  },
  {
      "name": "Hopkinton",
    "state_code": "MA",
    "zip": "01748",
    "latitude": 42.219046,
    "longitude": -71.530178,
    "country": "Middlesex"
  },
  {
      "name": "Hudson",
    "state_code": "MA",
    "zip": "01749",
    "latitude": 42.391796,
    "longitude": -71.560896,
    "country": "Middlesex"
  },
  {
      "name": "Marlborough",
    "state_code": "MA",
    "zip": "01752",
    "latitude": 42.350861,
    "longitude": -71.543355,
    "country": "Middlesex"
  },
  {
      "name": "Maynard",
    "state_code": "MA",
    "zip": "01754",
    "latitude": 42.432118,
    "longitude": -71.454975,
    "country": "Middlesex"
  },
  {
      "name": "Mendon",
    "state_code": "MA",
    "zip": "01756",
    "latitude": 42.096744,
    "longitude": -71.549882,
    "country": "Worcester"
  },
  {
      "name": "Milford",
    "state_code": "MA",
    "zip": "01757",
    "latitude": 42.151142,
    "longitude": -71.527402,
    "country": "Worcester"
  },
  {
      "name": "Natick",
    "state_code": "MA",
    "zip": "01760",
    "latitude": 42.287476,
    "longitude": -71.35741,
    "country": "Middlesex"
  },
  {
      "name": "Sherborn",
    "state_code": "MA",
    "zip": "01770",
    "latitude": 42.233088,
    "longitude": -71.378717,
    "country": "Middlesex"
  },
  {
      "name": "Southborough",
    "state_code": "MA",
    "zip": "01772",
    "latitude": 42.293919,
    "longitude": -71.531997,
    "country": "Worcester"
  },
  {
      "name": "Lincoln",
    "state_code": "MA",
    "zip": "01773",
    "latitude": 42.421723,
    "longitude": -71.313723,
    "country": "Middlesex"
  },
  {
      "name": "Stow",
    "state_code": "MA",
    "zip": "01775",
    "latitude": 42.430785,
    "longitude": -71.515019,
    "country": "Middlesex"
  },
  {
      "name": "Sudbury",
    "state_code": "MA",
    "zip": "01776",
    "latitude": 42.383655,
    "longitude": -71.428159,
    "country": "Middlesex"
  },
  {
      "name": "Wayland",
    "state_code": "MA",
    "zip": "01778",
    "latitude": 42.348629,
    "longitude": -71.358781,
    "country": "Middlesex"
  },
  {
      "name": "Woodville",
    "state_code": "MA",
    "zip": "01784",
    "latitude": 42.2375,
    "longitude": -71.5625,
    "country": "Middlesex"
  },
  {
      "name": "Woburn",
    "state_code": "MA",
    "zip": "01801",
    "latitude": 42.482894,
    "longitude": -71.157404,
    "country": "Middlesex"
  },
  {
      "name": "Burlington",
    "state_code": "MA",
    "zip": "01803",
    "latitude": 42.508942,
    "longitude": -71.200437,
    "country": "Middlesex"
  },
  {
      "name": "Andover",
    "state_code": "MA",
    "zip": "01810",
    "latitude": 42.64956,
    "longitude": -71.156481,
    "country": "Essex"
  },
  {
      "name": "Billerica",
    "state_code": "MA",
    "zip": "01821",
    "latitude": 42.551874,
    "longitude": -71.251754,
    "country": "Middlesex"
  },
  {
      "name": "Chelmsford",
    "state_code": "MA",
    "zip": "01824",
    "latitude": 42.59356,
    "longitude": -71.357521,
    "country": "Middlesex"
  },
  {
      "name": "Dracut",
    "state_code": "MA",
    "zip": "01826",
    "latitude": 42.676422,
    "longitude": -71.318592,
    "country": "Middlesex"
  },
  {
      "name": "Dunstable",
    "state_code": "MA",
    "zip": "01827",
    "latitude": 42.673917,
    "longitude": -71.495201,
    "country": "Middlesex"
  },
  {
      "name": "Haverhill",
    "state_code": "MA",
    "zip": "01830",
    "latitude": 42.785605,
    "longitude": -71.072057,
    "country": "Essex"
  },
  {
      "name": "Georgetown",
    "state_code": "MA",
    "zip": "01833",
    "latitude": 42.728067,
    "longitude": -70.982239,
    "country": "Essex"
  },
  {
      "name": "Groveland",
    "state_code": "MA",
    "zip": "01834",
    "latitude": 42.753027,
    "longitude": -71.027018,
    "country": "Essex"
  },
  {
      "name": "Lawrence",
    "state_code": "MA",
    "zip": "01840",
    "latitude": 42.707958,
    "longitude": -71.16381,
    "country": "Essex"
  },
  {
      "name": "Methuen",
    "state_code": "MA",
    "zip": "01844",
    "latitude": 42.728019,
    "longitude": -71.181031,
    "country": "Essex"
  },
  {
      "name": "North Andover",
    "state_code": "MA",
    "zip": "01845",
    "latitude": 42.682583,
    "longitude": -71.109004,
    "country": "Essex"
  },
  {
      "name": "Lowell",
    "state_code": "MA",
    "zip": "01850",
    "latitude": 42.656035,
    "longitude": -71.305078,
    "country": "Middlesex"
  },
  {
      "name": "Merrimac",
    "state_code": "MA",
    "zip": "01860",
    "latitude": 42.834629,
    "longitude": -71.004658,
    "country": "Essex"
  },
  {
      "name": "North Billerica",
    "state_code": "MA",
    "zip": "01862",
    "latitude": 42.575694,
    "longitude": -71.290217,
    "country": "Middlesex"
  },
  {
      "name": "North Chelmsford",
    "state_code": "MA",
    "zip": "01863",
    "latitude": 42.634737,
    "longitude": -71.390834,
    "country": "Middlesex"
  },
  {
      "name": "North Reading",
    "state_code": "MA",
    "zip": "01864",
    "latitude": 42.581898,
    "longitude": -71.094711,
    "country": "Middlesex"
  },
  {
      "name": "Nutting Lake",
    "state_code": "MA",
    "zip": "01865",
    "latitude": 42.537778,
    "longitude": -71.269444,
    "country": "Middlesex"
  },
  {
      "name": "Pinehurst",
    "state_code": "MA",
    "zip": "01866",
    "latitude": 42.529167,
    "longitude": -71.228611,
    "country": "Middlesex"
  },
  {
      "name": "Reading",
    "state_code": "MA",
    "zip": "01867",
    "latitude": 42.527986,
    "longitude": -71.109021,
    "country": "Middlesex"
  },
  {
      "name": "Tewksbury",
    "state_code": "MA",
    "zip": "01876",
    "latitude": 42.60283,
    "longitude": -71.223224,
    "country": "Middlesex"
  },
  {
      "name": "Tyngsboro",
    "state_code": "MA",
    "zip": "01879",
    "latitude": 42.672383,
    "longitude": -71.415766,
    "country": "Middlesex"
  },
  {
      "name": "Wakefield",
    "state_code": "MA",
    "zip": "01880",
    "latitude": 42.500886,
    "longitude": -71.068471,
    "country": "Middlesex"
  },
  {
      "name": "West Boxford",
    "state_code": "MA",
    "zip": "01885",
    "latitude": 42.706944,
    "longitude": -71.064444,
    "country": "Essex"
  },
  {
      "name": "Westford",
    "state_code": "MA",
    "zip": "01886",
    "latitude": 42.589959,
    "longitude": -71.438143,
    "country": "Middlesex"
  },
  {
      "name": "Wilmington",
    "state_code": "MA",
    "zip": "01887",
    "latitude": 42.558143,
    "longitude": -71.172306,
    "country": "Middlesex"
  },
  {
      "name": "Winchester",
    "state_code": "MA",
    "zip": "01890",
    "latitude": 42.453028,
    "longitude": -71.14407,
    "country": "Middlesex"
  },
  {
      "name": "Lynn",
    "state_code": "MA",
    "zip": "01901",
    "latitude": 42.463378,
    "longitude": -70.945516,
    "country": "Essex"
  },
  {
      "name": "Saugus",
    "state_code": "MA",
    "zip": "01906",
    "latitude": 42.463344,
    "longitude": -71.011093,
    "country": "Essex"
  },
  {
      "name": "Swampscott",
    "state_code": "MA",
    "zip": "01907",
    "latitude": 42.474611,
    "longitude": -70.909774,
    "country": "Essex"
  },
  {
      "name": "Nahant",
    "state_code": "MA",
    "zip": "01908",
    "latitude": 42.426098,
    "longitude": -70.927739,
    "country": "Essex"
  },
  {
      "name": "Amesbury",
    "state_code": "MA",
    "zip": "01913",
    "latitude": 42.855879,
    "longitude": -70.936681,
    "country": "Essex"
  },
  {
      "name": "Beverly",
    "state_code": "MA",
    "zip": "01915",
    "latitude": 42.560825,
    "longitude": -70.875939,
    "country": "Essex"
  },
  {
      "name": "Boxford",
    "state_code": "MA",
    "zip": "01921",
    "latitude": 42.679719,
    "longitude": -71.011372,
    "country": "Essex"
  },
  {
      "name": "Byfield",
    "state_code": "MA",
    "zip": "01922",
    "latitude": 42.756792,
    "longitude": -70.935053,
    "country": "Essex"
  },
  {
      "name": "Danvers",
    "state_code": "MA",
    "zip": "01923",
    "latitude": 42.569402,
    "longitude": -70.942461,
    "country": "Essex"
  },
  {
      "name": "Essex",
    "state_code": "MA",
    "zip": "01929",
    "latitude": 42.628629,
    "longitude": -70.782794,
    "country": "Essex"
  },
  {
      "name": "Gloucester",
    "state_code": "MA",
    "zip": "01930",
    "latitude": 42.620836,
    "longitude": -70.672149,
    "country": "Essex"
  },
  {
      "name": "Hamilton",
    "state_code": "MA",
    "zip": "01936",
    "latitude": 42.618333,
    "longitude": -70.856667,
    "country": "Essex"
  },
  {
      "name": "Hathorne",
    "state_code": "MA",
    "zip": "01937",
    "latitude": 42.586111,
    "longitude": -70.975,
    "country": "Essex"
  },
  {
      "name": "Ipswich",
    "state_code": "MA",
    "zip": "01938",
    "latitude": 42.680877,
    "longitude": -70.849353,
    "country": "Essex"
  },
  {
      "name": "Lynnfield",
    "state_code": "MA",
    "zip": "01940",
    "latitude": 42.532711,
    "longitude": -71.033873,
    "country": "Essex"
  },
  {
      "name": "Manchester",
    "state_code": "MA",
    "zip": "01944",
    "latitude": 42.57963,
    "longitude": -70.767434,
    "country": "Essex"
  },
  {
      "name": "Marblehead",
    "state_code": "MA",
    "zip": "01945",
    "latitude": 42.498431,
    "longitude": -70.865291,
    "country": "Essex"
  },
  {
      "name": "Middleton",
    "state_code": "MA",
    "zip": "01949",
    "latitude": 42.594184,
    "longitude": -71.013004,
    "country": "Essex"
  },
  {
      "name": "Newburyport",
    "state_code": "MA",
    "zip": "01950",
    "latitude": 42.812964,
    "longitude": -70.884668,
    "country": "Essex"
  },
  {
      "name": "Newbury",
    "state_code": "MA",
    "zip": "01951",
    "latitude": 42.783475,
    "longitude": -70.847377,
    "country": "Essex"
  },
  {
      "name": "Salisbury",
    "state_code": "MA",
    "zip": "01952",
    "latitude": 42.850678,
    "longitude": -70.858822,
    "country": "Essex"
  },
  {
      "name": "Peabody",
    "state_code": "MA",
    "zip": "01960",
    "latitude": 42.532579,
    "longitude": -70.961194,
    "country": "Essex"
  },
  {
      "name": "Prides Crossing",
    "state_code": "MA",
    "zip": "01965",
    "latitude": 42.559722,
    "longitude": -70.825,
    "country": "Essex"
  },
  {
      "name": "Rockport",
    "state_code": "MA",
    "zip": "01966",
    "latitude": 42.657973,
    "longitude": -70.619424,
    "country": "Essex"
  },
  {
      "name": "Rowley",
    "state_code": "MA",
    "zip": "01969",
    "latitude": 42.713753,
    "longitude": -70.90696,
    "country": "Essex"
  },
  {
      "name": "Salem",
    "state_code": "MA",
    "zip": "01970",
    "latitude": 42.515114,
    "longitude": -70.900343,
    "country": "Essex"
  },
  {
      "name": "South Hamilton",
    "state_code": "MA",
    "zip": "01982",
    "latitude": 42.618478,
    "longitude": -70.856132,
    "country": "Essex"
  },
  {
      "name": "Topsfield",
    "state_code": "MA",
    "zip": "01983",
    "latitude": 42.641546,
    "longitude": -70.948843,
    "country": "Essex"
  },
  {
      "name": "Wenham",
    "state_code": "MA",
    "zip": "01984",
    "latitude": 42.60166,
    "longitude": -70.878622,
    "country": "Essex"
  },
  {
      "name": "West Newbury",
    "state_code": "MA",
    "zip": "01985",
    "latitude": 42.794865,
    "longitude": -70.977811,
    "country": "Essex"
  },
  {
      "name": "Accord",
    "state_code": "MA",
    "zip": "02018",
    "latitude": 42.1747,
    "longitude": -70.8871,
    "country": "Plymouth"
  },
  {
      "name": "Bellingham",
    "state_code": "MA",
    "zip": "02019",
    "latitude": 42.074573,
    "longitude": -71.476829,
    "country": "Norfolk"
  },
  {
      "name": "Brant Rock",
    "state_code": "MA",
    "zip": "02020",
    "latitude": 42.086111,
    "longitude": -70.641667,
    "country": "Plymouth"
  },
  {
      "name": "Canton",
    "state_code": "MA",
    "zip": "02021",
    "latitude": 42.164454,
    "longitude": -71.135536,
    "country": "Norfolk"
  },
  {
      "name": "Cohasset",
    "state_code": "MA",
    "zip": "02025",
    "latitude": 42.239484,
    "longitude": -70.812788,
    "country": "Norfolk"
  },
  {
      "name": "Dedham",
    "state_code": "MA",
    "zip": "02026",
    "latitude": 42.243685,
    "longitude": -71.163741,
    "country": "Norfolk"
  },
  {
      "name": "Dover",
    "state_code": "MA",
    "zip": "02030",
    "latitude": 42.236233,
    "longitude": -71.285363,
    "country": "Norfolk"
  },
  {
      "name": "East Mansfield",
    "state_code": "MA",
    "zip": "02031",
    "latitude": 42.023056,
    "longitude": -71.177778,
    "country": "Bristol"
  },
  {
      "name": "East Walpole",
    "state_code": "MA",
    "zip": "02032",
    "latitude": 42.15324,
    "longitude": -71.2179,
    "country": "Norfolk"
  },
  {
      "name": "Foxboro",
    "state_code": "MA",
    "zip": "02035",
    "latitude": 42.064938,
    "longitude": -71.244127,
    "country": "Norfolk"
  },
  {
      "name": "Franklin",
    "state_code": "MA",
    "zip": "02038",
    "latitude": 42.09347,
    "longitude": -71.405786,
    "country": "Norfolk"
  },
  {
      "name": "Greenbush",
    "state_code": "MA",
    "zip": "02040",
    "latitude": 42.179167,
    "longitude": -70.75,
    "country": "Plymouth"
  },
  {
      "name": "Green Harbor",
    "state_code": "MA",
    "zip": "02041",
    "latitude": 42.077778,
    "longitude": -70.65,
    "country": "Plymouth"
  },
  {
      "name": "Hingham",
    "state_code": "MA",
    "zip": "02043",
    "latitude": 42.224485,
    "longitude": -70.891051,
    "country": "Plymouth"
  },
  {
      "name": "Hull",
    "state_code": "MA",
    "zip": "02045",
    "latitude": 42.285346,
    "longitude": -70.875442,
    "country": "Plymouth"
  },
  {
      "name": "Humarock",
    "state_code": "MA",
    "zip": "02047",
    "latitude": 42.136111,
    "longitude": -70.690556,
    "country": "Plymouth"
  },
  {
      "name": "Mansfield",
    "state_code": "MA",
    "zip": "02048",
    "latitude": 42.021238,
    "longitude": -71.217775,
    "country": "Bristol"
  },
  {
      "name": "Marshfield",
    "state_code": "MA",
    "zip": "02050",
    "latitude": 42.106177,
    "longitude": -70.69931,
    "country": "Plymouth"
  },
  {
      "name": "Marshfield Hills",
    "state_code": "MA",
    "zip": "02051",
    "latitude": 42.133333,
    "longitude": -70.758333,
    "country": "Plymouth"
  },
  {
      "name": "Medfield",
    "state_code": "MA",
    "zip": "02052",
    "latitude": 42.184525,
    "longitude": -71.304813,
    "country": "Norfolk"
  },
  {
      "name": "Medway",
    "state_code": "MA",
    "zip": "02053",
    "latitude": 42.151363,
    "longitude": -71.421715,
    "country": "Norfolk"
  },
  {
      "name": "Millis",
    "state_code": "MA",
    "zip": "02054",
    "latitude": 42.166938,
    "longitude": -71.360693,
    "country": "Norfolk"
  },
  {
      "name": "Minot",
    "state_code": "MA",
    "zip": "02055",
    "latitude": 42.195833,
    "longitude": -70.726389,
    "country": "Plymouth"
  },
  {
      "name": "Norfolk",
    "state_code": "MA",
    "zip": "02056",
    "latitude": 42.117746,
    "longitude": -71.326934,
    "country": "Norfolk"
  },
  {
      "name": "North Marshfield",
    "state_code": "MA",
    "zip": "02059",
    "latitude": 42.143056,
    "longitude": -70.770833,
    "country": "Plymouth"
  },
  {
      "name": "North Scituate",
    "state_code": "MA",
    "zip": "02060",
    "latitude": 42.218889,
    "longitude": -70.786111,
    "country": "Plymouth"
  },
  {
      "name": "Norwell",
    "state_code": "MA",
    "zip": "02061",
    "latitude": 42.159574,
    "longitude": -70.82172,
    "country": "Plymouth"
  },
  {
      "name": "Norwood",
    "state_code": "MA",
    "zip": "02062",
    "latitude": 42.186843,
    "longitude": -71.203313,
    "country": "Norfolk"
  },
  {
      "name": "Ocean Bluff",
    "state_code": "MA",
    "zip": "02065",
    "latitude": 42.097222,
    "longitude": -70.654167,
    "country": "Plymouth"
  },
  {
      "name": "Scituate",
    "state_code": "MA",
    "zip": "02066",
    "latitude": 42.203235,
    "longitude": -70.752476,
    "country": "Plymouth"
  },
  {
      "name": "Sharon",
    "state_code": "MA",
    "zip": "02067",
    "latitude": 42.109388,
    "longitude": -71.175872,
    "country": "Norfolk"
  },
  {
      "name": "Sheldonville",
    "state_code": "MA",
    "zip": "02070",
    "latitude": 42.034722,
    "longitude": -71.3875,
    "country": "Norfolk"
  },
  {
      "name": "South Walpole",
    "state_code": "MA",
    "zip": "02071",
    "latitude": 42.099203,
    "longitude": -71.275235,
    "country": "Norfolk"
  },
  {
      "name": "Stoughton",
    "state_code": "MA",
    "zip": "02072",
    "latitude": 42.125279,
    "longitude": -71.107357,
    "country": "Norfolk"
  },
  {
      "name": "Walpole",
    "state_code": "MA",
    "zip": "02081",
    "latitude": 42.144413,
    "longitude": -71.254391,
    "country": "Norfolk"
  },
  {
      "name": "Westwood",
    "state_code": "MA",
    "zip": "02090",
    "latitude": 42.214824,
    "longitude": -71.210426,
    "country": "Norfolk"
  },
  {
      "name": "Wrentham",
    "state_code": "MA",
    "zip": "02093",
    "latitude": 42.061746,
    "longitude": -71.339568,
    "country": "Norfolk"
  },
  {
      "name": "Boston",
    "state_code": "MA",
    "zip": "02108",
    "latitude": 42.357603,
    "longitude": -71.068432,
    "country": "Suffolk"
  },
  {
      "name": "Mattapan",
    "state_code": "MA",
    "zip": "02126",
    "latitude": 42.273889,
    "longitude": -71.093871,
    "country": "Suffolk"
  },
  {
      "name": "Charlestown",
    "state_code": "MA",
    "zip": "02129",
    "latitude": 42.377815,
    "longitude": -71.062715,
    "country": "Suffolk"
  },
  {
      "name": "Jamaica Plain",
    "state_code": "MA",
    "zip": "02130",
    "latitude": 42.312596,
    "longitude": -71.111495,
    "country": "Suffolk"
  },
  {
      "name": "Roslindale",
    "state_code": "MA",
    "zip": "02131",
    "latitude": 42.283615,
    "longitude": -71.129543,
    "country": "Suffolk"
  },
  {
      "name": "West Roxbury",
    "state_code": "MA",
    "zip": "02132",
    "latitude": 42.27868,
    "longitude": -71.158868,
    "country": "Suffolk"
  },
  {
      "name": "Allston",
    "state_code": "MA",
    "zip": "02134",
    "latitude": 42.353519,
    "longitude": -71.132866,
    "country": "Suffolk"
  },
  {
      "name": "Brighton",
    "state_code": "MA",
    "zip": "02135",
    "latitude": 42.34779,
    "longitude": -71.156599,
    "country": "Suffolk"
  },
  {
      "name": "Hyde Park",
    "state_code": "MA",
    "zip": "02136",
    "latitude": 42.253989,
    "longitude": -71.126052,
    "country": "Suffolk"
  },
  {
      "name": "Readville",
    "state_code": "MA",
    "zip": "02137",
    "latitude": 42.249167,
    "longitude": -71.229167,
    "country": "Suffolk"
  },
  {
      "name": "Cambridge",
    "state_code": "MA",
    "zip": "02138",
    "latitude": 42.377045,
    "longitude": -71.125611,
    "country": "Middlesex"
  },
  {
      "name": "Somerville",
    "state_code": "MA",
    "zip": "02143",
    "latitude": 42.382945,
    "longitude": -71.102814,
    "country": "Middlesex"
  },
  {
      "name": "Malden",
    "state_code": "MA",
    "zip": "02148",
    "latitude": 42.42911,
    "longitude": -71.060507,
    "country": "Middlesex"
  },
  {
      "name": "Everett",
    "state_code": "MA",
    "zip": "02149",
    "latitude": 42.411199,
    "longitude": -71.051448,
    "country": "Middlesex"
  },
  {
      "name": "Chelsea",
    "state_code": "MA",
    "zip": "02150",
    "latitude": 42.396252,
    "longitude": -71.032521,
    "country": "Suffolk"
  },
  {
      "name": "Revere",
    "state_code": "MA",
    "zip": "02151",
    "latitude": 42.413767,
    "longitude": -71.005165,
    "country": "Suffolk"
  },
  {
      "name": "Winthrop",
    "state_code": "MA",
    "zip": "02152",
    "latitude": 42.376294,
    "longitude": -70.980043,
    "country": "Suffolk"
  },
  {
      "name": "Medford",
    "state_code": "MA",
    "zip": "02153",
    "latitude": 42.418333,
    "longitude": -71.106667,
    "country": "Middlesex"
  },
  {
      "name": "West Medford",
    "state_code": "MA",
    "zip": "02156",
    "latitude": 42.422222,
    "longitude": -71.133333,
    "country": "Middlesex"
  },
  {
      "name": "Quincy",
    "state_code": "MA",
    "zip": "02169",
    "latitude": 42.249133,
    "longitude": -70.997816,
    "country": "Norfolk"
  },
  {
      "name": "Melrose",
    "state_code": "MA",
    "zip": "02176",
    "latitude": 42.458066,
    "longitude": -71.063191,
    "country": "Middlesex"
  },
  {
      "name": "Stoneham",
    "state_code": "MA",
    "zip": "02180",
    "latitude": 42.482778,
    "longitude": -71.0978,
    "country": "Middlesex"
  },
  {
      "name": "Braintree",
    "state_code": "MA",
    "zip": "02184",
    "latitude": 42.209284,
    "longitude": -70.996304,
    "country": "Norfolk"
  },
  {
      "name": "Milton",
    "state_code": "MA",
    "zip": "02186",
    "latitude": 42.253663,
    "longitude": -71.077051,
    "country": "Norfolk"
  },
  {
      "name": "Milton Village",
    "state_code": "MA",
    "zip": "02187",
    "latitude": 42.266667,
    "longitude": -71.072222,
    "country": "Norfolk"
  },
  {
      "name": "Weymouth",
    "state_code": "MA",
    "zip": "02188",
    "latitude": 42.211327,
    "longitude": -70.958248,
    "country": "Norfolk"
  },
  {
      "name": "East Weymouth",
    "state_code": "MA",
    "zip": "02189",
    "latitude": 42.211606,
    "longitude": -70.931671,
    "country": "Norfolk"
  },
  {
      "name": "South Weymouth",
    "state_code": "MA",
    "zip": "02190",
    "latitude": 42.172817,
    "longitude": -70.94869,
    "country": "Norfolk"
  },
  {
      "name": "North Weymouth",
    "state_code": "MA",
    "zip": "02191",
    "latitude": 42.243564,
    "longitude": -70.944318,
    "country": "Norfolk"
  },
  {
      "name": "East Boston",
    "state_code": "MA",
    "zip": "02228",
    "latitude": 42.375,
    "longitude": -71.039722,
    "country": "Suffolk"
  },
  {
      "name": "Brockton",
    "state_code": "MA",
    "zip": "02301",
    "latitude": 42.08,
    "longitude": -71.0377,
    "country": "Plymouth"
  },
  {
      "name": "Avon",
    "state_code": "MA",
    "zip": "02322",
    "latitude": 42.125825,
    "longitude": -71.043738,
    "country": "Norfolk"
  },
  {
      "name": "Bridgewater",
    "state_code": "MA",
    "zip": "02324",
    "latitude": 41.977341,
    "longitude": -70.97234,
    "country": "Plymouth"
  },
  {
      "name": "Bryantville",
    "state_code": "MA",
    "zip": "02327",
    "latitude": 42.043889,
    "longitude": -70.843056,
    "country": "Plymouth"
  },
  {
      "name": "Carver",
    "state_code": "MA",
    "zip": "02330",
    "latitude": 41.888265,
    "longitude": -70.767754,
    "country": "Plymouth"
  },
  {
      "name": "Duxbury",
    "state_code": "MA",
    "zip": "02331",
    "latitude": 42.041667,
    "longitude": -70.672778,
    "country": "Plymouth"
  },
  {
      "name": "East Bridgewater",
    "state_code": "MA",
    "zip": "02333",
    "latitude": 42.031478,
    "longitude": -70.944964,
    "country": "Plymouth"
  },
  {
      "name": "Easton",
    "state_code": "MA",
    "zip": "02334",
    "latitude": 42.024444,
    "longitude": -71.129167,
    "country": "Bristol"
  },
  {
      "name": "Elmwood",
    "state_code": "MA",
    "zip": "02337",
    "latitude": 42.009722,
    "longitude": -70.9625,
    "country": "Plymouth"
  },
  {
      "name": "Halifax",
    "state_code": "MA",
    "zip": "02338",
    "latitude": 42.000159,
    "longitude": -70.844794,
    "country": "Plymouth"
  },
  {
      "name": "Hanover",
    "state_code": "MA",
    "zip": "02339",
    "latitude": 42.121406,
    "longitude": -70.857006,
    "country": "Plymouth"
  },
  {
      "name": "Hanson",
    "state_code": "MA",
    "zip": "02341",
    "latitude": 42.061627,
    "longitude": -70.865053,
    "country": "Plymouth"
  },
  {
      "name": "Holbrook",
    "state_code": "MA",
    "zip": "02343",
    "latitude": 42.14641,
    "longitude": -71.008273,
    "country": "Norfolk"
  },
  {
      "name": "Middleboro",
    "state_code": "MA",
    "zip": "02344",
    "latitude": 41.8938,
    "longitude": -70.9187,
    "country": "Plymouth"
  },
  {
      "name": "Manomet",
    "state_code": "MA",
    "zip": "02345",
    "latitude": 41.918611,
    "longitude": -70.566667,
    "country": "Plymouth"
  },
  {
      "name": "Lakeville",
    "state_code": "MA",
    "zip": "02347",
    "latitude": 41.837377,
    "longitude": -70.958195,
    "country": "Plymouth"
  },
  {
      "name": "Monponsett",
    "state_code": "MA",
    "zip": "02350",
    "latitude": 42.018333,
    "longitude": -70.847222,
    "country": "Plymouth"
  },
  {
      "name": "Abington",
    "state_code": "MA",
    "zip": "02351",
    "latitude": 42.116715,
    "longitude": -70.954293,
    "country": "Plymouth"
  },
  {
      "name": "North Carver",
    "state_code": "MA",
    "zip": "02355",
    "latitude": 41.920278,
    "longitude": -70.8,
    "country": "Plymouth"
  },
  {
      "name": "North Easton",
    "state_code": "MA",
    "zip": "02356",
    "latitude": 42.058956,
    "longitude": -71.112337,
    "country": "Bristol"
  },
  {
      "name": "North Pembroke",
    "state_code": "MA",
    "zip": "02358",
    "latitude": 42.093056,
    "longitude": -70.793056,
    "country": "Plymouth"
  },
  {
      "name": "Pembroke",
    "state_code": "MA",
    "zip": "02359",
    "latitude": 42.062072,
    "longitude": -70.804404,
    "country": "Plymouth"
  },
  {
      "name": "Plymouth",
    "state_code": "MA",
    "zip": "02360",
    "latitude": 41.910404,
    "longitude": -70.642004,
    "country": "Plymouth"
  },
  {
      "name": "Kingston",
    "state_code": "MA",
    "zip": "02364",
    "latitude": 41.995022,
    "longitude": -70.740993,
    "country": "Plymouth"
  },
  {
      "name": "South Carver",
    "state_code": "MA",
    "zip": "02366",
    "latitude": 41.840556,
    "longitude": -70.747222,
    "country": "Plymouth"
  },
  {
      "name": "Plympton",
    "state_code": "MA",
    "zip": "02367",
    "latitude": 41.96549,
    "longitude": -70.804582,
    "country": "Plymouth"
  },
  {
      "name": "Randolph",
    "state_code": "MA",
    "zip": "02368",
    "latitude": 42.173587,
    "longitude": -71.051392,
    "country": "Norfolk"
  },
  {
      "name": "Rockland",
    "state_code": "MA",
    "zip": "02370",
    "latitude": 42.129286,
    "longitude": -70.913263,
    "country": "Plymouth"
  },
  {
      "name": "South Easton",
    "state_code": "MA",
    "zip": "02375",
    "latitude": 42.025704,
    "longitude": -71.098814,
    "country": "Bristol"
  },
  {
      "name": "West Bridgewater",
    "state_code": "MA",
    "zip": "02379",
    "latitude": 42.025511,
    "longitude": -71.016054,
    "country": "Plymouth"
  },
  {
      "name": "White Horse Beach",
    "state_code": "MA",
    "zip": "02381",
    "latitude": 41.930833,
    "longitude": -70.558333,
    "country": "Plymouth"
  },
  {
      "name": "Whitman",
    "state_code": "MA",
    "zip": "02382",
    "latitude": 42.081603,
    "longitude": -70.938127,
    "country": "Plymouth"
  },
  {
      "name": "Lexington",
    "state_code": "MA",
    "zip": "02420",
    "latitude": 42.4558,
    "longitude": -71.2193,
    "country": "Middlesex"
  },
  {
      "name": "Brookline",
    "state_code": "MA",
    "zip": "02445",
    "latitude": 42.3302,
    "longitude": -71.1304,
    "country": "Norfolk"
  },
  {
      "name": "Brookline Village",
    "state_code": "MA",
    "zip": "02447",
    "latitude": 42.332778,
    "longitude": -71.116667,
    "country": "Norfolk"
  },
  {
      "name": "Waltham",
    "state_code": "MA",
    "zip": "02451",
    "latitude": 42.3954,
    "longitude": -71.2508,
    "country": "Middlesex"
  },
  {
      "name": "North Waltham",
    "state_code": "MA",
    "zip": "02455",
    "latitude": 42.393056,
    "longitude": -71.220278,
    "country": "Middlesex"
  },
  {
      "name": "New Town",
    "state_code": "MA",
    "zip": "02456",
    "latitude": 42.36,
    "longitude": -71.130556,
    "country": "Middlesex"
  },
  {
      "name": "Babson Park",
    "state_code": "MA",
    "zip": "02457",
    "latitude": 42.298611,
    "longitude": -71.26,
    "country": "Norfolk"
  },
  {
      "name": "Newton",
    "state_code": "MA",
    "zip": "02458",
    "latitude": 42.3545,
    "longitude": -71.1877,
    "country": "Middlesex"
  },
  {
      "name": "Newton Center",
    "state_code": "MA",
    "zip": "02459",
    "latitude": 42.336944,
    "longitude": -71.209722,
    "country": "Middlesex"
  },
  {
      "name": "Newtonville",
    "state_code": "MA",
    "zip": "02460",
    "latitude": 42.3521,
    "longitude": -71.2081,
    "country": "Middlesex"
  },
  {
      "name": "Newton Highlands",
    "state_code": "MA",
    "zip": "02461",
    "latitude": 42.320833,
    "longitude": -71.2,
    "country": "Middlesex"
  },
  {
      "name": "Newton Lower Falls",
    "state_code": "MA",
    "zip": "02462",
    "latitude": 42.329167,
    "longitude": -71.254167,
    "country": "Middlesex"
  },
  {
      "name": "Newton Upper Falls",
    "state_code": "MA",
    "zip": "02464",
    "latitude": 42.313889,
    "longitude": -71.22,
    "country": "Middlesex"
  },
  {
      "name": "West Newton",
    "state_code": "MA",
    "zip": "02465",
    "latitude": 42.35,
    "longitude": -71.233333,
    "country": "Middlesex"
  }
]';
        $cities = json_decode($citiesJSON);

        foreach ($cities as $cityItem) {
            $city = new City();
            $city->setName($cityItem->name);
            $city->setStateCode($cityItem->state_code);
            $city->setZip($cityItem->zip);
            $city->setLatitude($cityItem->latitude);
            $city->setLongitude($cityItem->longitude);
            $city->setCountry($cityItem->country);
            $manager->persist($city);
            $manager->flush();
        }
    }
}